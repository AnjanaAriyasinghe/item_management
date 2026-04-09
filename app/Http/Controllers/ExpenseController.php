<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseSubCategory;
use App\Models\ExpensReference;
use App\Models\Vendor;
use App\Traits\ImageUpload;
use App\Traits\PdfUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ExpenseController extends Controller
{
    use ImageUpload, PdfUpload;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:finance-expenses-module-module|finance-expenses-module-create')->only('index');
        $this->middleware('permission:finance-expenses-module-edit')->only(['edit', 'update']);
        $this->middleware('permission:finance-expenses-module-delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Expense::with('category', 'sub_category', 'created_by' ,'vendor','vendor_account','company')->latest('id');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('company', function ($data) {
                        return $data->company?->name

                        ? '<span class="badge bg-success text-center">' . $data->company->name . '</span>'
                        : '';
                    })
                    ->addColumn('status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->status;
                        switch ($status) {
                            case 'pending':
                                $badge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 'approved':
                                $badge = '<span class="badge bg-success">Approved</span>';
                                break;
                            case 'rejected':
                                $badge = '<span class="badge bg-danger">Rejected</span>';
                                break;
                            case 'paid':
                                $badge = '<span class="badge bg-success-1">Paid</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }

                        return $badge;
                    })
                    ->addColumn('paymnet_status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->paymnet_status;
                        switch ($status) {
                            case 'pending':
                                $badge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 'partially':
                                $badge = '<span class="badge bg-primary">Partially</span>';
                                break;
                            case 'complete':
                                $badge = '<span class="badge bg-success-1">Complete</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }

                        return $badge;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('finance-expenses-module-edit')) {
                            if ($data->status == 'pending') {
                                $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                            }
                        }
                        if (auth()->user()->can('finance-expenses-module-view')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if (auth()->user()->can('finance-expenses-module-delete')) {
                            if ($data->status == 'pending') {
                                $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('finance.expenses.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                            }
                        }
                        // $buttons .= '<a href="' . Storage::url($data->pdf) . '" download>Download PDF</a>';
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'image', 'paymnet_status', 'status','company'])
                    ->make(true);
            }
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies = Company::all();
            }
            $defaultCompany = auth()->user()->defaultCompany;

            return view('pages.finance.expense.index', ['companies'=>$companies, 'defaultCompany' => $defaultCompany->id]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseRequest $request)
    {
        try {
            $input = $request->all();
            DB::beginTransaction();
            if ($request->file('pdf')) {
                $input['pdf'] = $this->save_pdf($request->file('pdf'), 'epenses_pdf', $request->file('old_pdf'));
            }
            $input['created_by'] = auth()->user()->id;
            $input['balance'] = $request->amount;
            $expense = Expense::create($input);
            DB::commit();
            if ($expense) {
                return response()->json(['message' => "Expense created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        try {
            $data = Expense::with('category', 'sub_category', 'created_by', 'updated_by', 'approved_by','rejected_by','vendor','vendor_account')->find($expense->id);
            return response()->json([
                'expense' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        try {
            $data = Expense::find($expense->id);
            return response()->json([
                'expense' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $expense = Expense::findOrFail($id);
            $input = $request->all();
            $input['updated_by'] = auth()->user()->id;
            $input['balance'] = $request->amount;
            $expense->update($input);
            if ($request->file('pdf')) {
                $input['pdf'] = $this->save_pdf($request->file('pdf'), 'epenses_pdf', $request->old_pdf);
            }
            DB::commit();
            if ($expense) {
                return response()->json(['message' => "Expense updated successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        try {
            $expense->update(['deleted_by' => auth()->user()->id]);
            $expense->delete();
            if ($expense) {
                return response()->json(['message' => "Expense deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function get_sub_category($id)
    {
        try {
            $sub_categories = ExpenseSubCategory::where('category_id', $id)->get();
            return response()->json([
                'sub_categories' => $sub_categories,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
