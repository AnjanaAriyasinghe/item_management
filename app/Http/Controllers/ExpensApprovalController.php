<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovedRequest;
use App\Http\Requests\RejectRequest;
use App\Models\Company;
use App\Models\Expense;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExpensApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:finance-expenses-approval-module')->only('index');
        $this->middleware('permission:report-access-expense_approved-view')->only('approved_list');
        $this->middleware('permission:report-access-expense_rejected-view')->only('rejected_list');
        $this->middleware('permission:finance-expenses-approval-rejected')->only(['edit', 'update']);

    }
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // if($request->company_id){
                //     $data = ExpenseSubCategory::with('category','created_user')
                //     ->when($request->category_id != 'all', function ($query) use ($request) {
                //         $query->whereHas('category', function ($query) use ($request) {
                //             $query->where('category_id', $request->category_id);
                //         });
                //     });
                // }else{
                //     $data=[];
                // }
                // dd($request);
                if ($request->company_id) {
                    $data = Expense::with('category', 'sub_category','vendor','created_by','company')
                    ->where('status', ['pending'])
                    ->latest('id')

                    ->when($request->company_id != 'all', function ($query) use ($request) {
                            $query->whereHas('company', function ($query) use ($request) {
                                $query->where('id', $request->company_id);
                            });
                    });
                }

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
                        if (auth()->user()->can('finance-expenses-approval-module')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if ($data->status == 'pending') {
                            if (auth()->user()->can('finance-expenses-approval-approve')) {
                                $buttons .= ' <button class="btn btn-success btn-sm btnApproval"  data-bs-toggle="modal" data-bs-target="#approvalModel" title="Approval" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-check"></i></button>';
                            }
                        }
                        if ($data->status == 'pending') {
                            if (auth()->user()->can('finance-expenses-approval-rejected')) {
                                $buttons .= ' <button class="btn btn-danger btn-sm btnReject"  data-bs-toggle="modal" data-bs-target="#rejectModel" title="Reject" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-x"></i></button>';
                            }
                        }
                        // if ($data->status == 'pending') {
                        //     if (auth()->user()->can('finance-expenses-approval-module')) {
                        //         $buttons .= ' <button class="btn btn-warning btn-sm btnCancel" title="Cancel" onclick="handleCancel(\'' . route('finance.expense.approval.cancel', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-arrow-back-up"></i></button>';
                        //     }
                        // }
                        // $buttons .= '<a href="' . Storage::url($data->pdf) . '" download>Download PDF</a>';
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'paymnet_status', 'status','company'])
                    ->make(true);
            }
            // $companies = auth()->user()->companies;
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies =  auth()->user()->companies;
            }
            $defaultCompany = auth()->user()->defaultCompany;
            return view('pages.finance.expense_approval.index', ['companies'=>$companies, 'defaultCompany' => $defaultCompany->id]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
    public function cancel(Expense $expense)
    {
        try {
            dd($expense);
            if ($expense) {
                return response()->json(['message' => "Vendor deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function approved(ApprovedRequest $request)
    {
        try {
            $expene = Expense::find($request->id);
            $expene->approved_by = auth()->user()->id;
            $expene->approved_date = now();
            $expene->approved_comment = $request->approval_comment;
            $expene->status = 'approved';
            $expene->save();
            if ($expene) {
                return response()->json(['message' => "Expense approved successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function rejected(RejectRequest $request)
    {
        try {
            $expene = Expense::find($request->id);
            $expene->rejected_by = auth()->user()->id;
            $expene->rejected_date = now();
            $expene->rejected_comment = $request->reject_comment;
            $expene->status = 'rejected';
            $expene->save();
            if ($expene) {
                return response()->json(['message' => "Expense rejected successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function approved_list(Request $request)
    {
        try {
            if ($request->ajax()) {
                // if ($request->company_id) {
                //     $data->where('company_id', $request->company_id);
                // }
                if ($request->company_id) {
                    $data = Expense::with('category', 'sub_category', 'approved_by', 'vendor','company')
                    ->where('status', ['approved'])
                    ->whereIn('paymnet_status', ['pending', 'partially'])
                    ->latest('id')
                    ->when($request->company_id != 'all', function ($query) use ($request) {
                            $query->whereHas('company', function ($query) use ($request) {
                                $query->where('id', $request->company_id);
                            });
                    });

                }
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
                        if (auth()->user()->can('finance-expenses-approval-module')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'paymnet_status', 'status','company'])
                    ->make(true);
            }
            // $companies = auth()->user()->companies;
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies =  auth()->user()->companies;
            }
            $defaultCompany = auth()->user()->defaultCompany;
            return view('pages.finance.expense.approved_list.index',['companies'=>$companies, 'defaultCompany' => $defaultCompany->id]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    public function rejected_list(Request $request)
    {
        try {
            if ($request->ajax()) {
                // $data = Expense::with('category', 'sub_category', 'rejected_by','vendor')->where('status', ['rejected'])->latest('id');
                // if ($request->company_id) {
                //     $data->where('company_id', $request->company_id);
                // }
                if ($request->company_id) {

                    $data = Expense::with('category', 'sub_category', 'rejected_by','vendor')
                    ->where('status', ['rejected'])
                    ->latest('id')
                    ->when($request->company_id != 'all', function ($query) use ($request) {
                        $query->whereHas('company', function ($query) use ($request) {
                            $query->where('id', $request->company_id);
                        });
                    });
                }
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
                        if (auth()->user()->can('finance-expenses-approval-module')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'paymnet_status', 'status','company'])
                    ->make(true);
            }
            // $companies = auth()->user()->companies;
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies =  auth()->user()->companies;
            }
            $defaultCompany = auth()->user()->defaultCompany;
            return view('pages.finance.expense.rejected_list.index', ['companies'=>$companies, 'defaultCompany' => $defaultCompany->id]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }


}
