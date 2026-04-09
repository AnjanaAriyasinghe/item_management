<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountRequest;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\BankBranch;
use App\Models\ChequeBook;
use App\Models\Company;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-bank_account-module|admin-common-bank_account-create')->only('index');
        $this->middleware('permission:admin-common-bank_account-edit')->only(['edit', 'update']);
        $this->middleware('permission:admin-common-bank_account-delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = BankAccount::with('bank', 'branch', 'created_user','company')->latest('id');
                // dd($data);
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('created_at', function ($data) {
                        return $data->updated_at ?? $data->created_at;
                    })
                    ->addColumn('company', function ($data) {
                        return $data->company?->name
                        // dd($company_name);
                        ? '<span class="badge bg-success text-center">' . $data->company->name . '</span>'
                        : '';
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-bank_account-edit')) {
                            $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-bank_account-delete')) {
                            if (!ChequeBook::where('bank_account_id', $data->id)->exists()) {
                                $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.bank_accounts.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                            }
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'image','company'])
                    ->make(true);
            }
            // $companies = Company::all();
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies =  auth()->user()->companies;
            }
            return view('pages.admin.bank_account.index', ['companies'=>$companies]);
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
    public function store(BankAccountRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['created_by'] = auth()->user()->id;
            $bankAccount = BankAccount::create($input);
            DB::commit();
            if ($bankAccount) {
                return response()->json(['message' => "Bank Account created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        try {
            $bankAccount = BankAccount::find($bankAccount->id);
            return response()->json([
                'bankAccount' => $bankAccount,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BankAccountRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $bankAccount = BankAccount::findOrFail($id);
            $input = $request->all();
            $input['updated_by'] = auth()->user()->id;
            $bankAccount->update($input);
            DB::commit();
            return response()->json(['message' => "Bank Account updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        try {
            $bankAccount->update(['deleted_by' => auth()->user()->id]);
            $bankAccount->delete();
            if ($bankAccount) {
                return response()->json(['message' => "Bank Account deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public final function getBanks(Request $request)
    {
        try {
            return Bank::all();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public final function getBranches(Request $request)
    {
        try {
            return BankBranch::where('bank_id', $request->id)->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function getVendors()
    {
        try {
            return Vendor::where('is_active','1')->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
    public function getAccountsByBanks($id)
    {
        try {
            return BankAccount::with('bank', 'branch')->where('bank_id', $id)->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
