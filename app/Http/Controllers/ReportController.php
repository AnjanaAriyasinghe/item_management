<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:report-access-payments-view')->only('payments');
        // $this->middleware('permission:admin-common-vendor-edit')->only(['edit', 'update']);
        // $this->middleware('permission:admin-common-vendor-delete')->only('destroy');
    }
    public function payments(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Payment::with('expense.category', 'expense.sub_category', 'vendor', 'bank_account', 'cheque_book', 'cheque_book_detail','company')->latest('id')
                    // ->when($request->company_id && $request->company_id !== 'all', function ($query) use ($request) {
                    //     $query->whereHas('company', function ($query) use ($request) {
                    //         $query->where('id', $request->company_id);
                    //     });
                    // })
                    ->when($request->company_id && $request->company_id !== 'all', function ($query) use ($request) {
                        $query->whereHas('company', function ($query) use ($request) {
                            // Specify the table name for the 'id' column to remove ambiguity
                            $query->where('companies.id', $request->company_id);
                        });
                    })
                    ->when($request->vendor_id && $request->vendor_id !== 'all', function ($query) use ($request) {
                        $query->whereHas('vendor', function ($query) use ($request) {
                            $query->where('id', $request->vendor_id);
                        });
                    })
                    ->when($request->cheque_book_id && $request->cheque_book_id !== 'all', function ($query) use ($request) {
                            $query->whereHas('cheque_book', function ($query) use ($request) {
                                $query->where('id', $request->cheque_book_id);
                            });
                        })
                    ->when($request->category_id && $request->category_id !== 'all', function ($query) use ($request) {
                        $query->whereHas('expense.category', function ($query) use ($request) {
                            $query->where('id', $request->category_id);
                        });
                    })
                    ->when($request->sub_category_id && $request->sub_category_id !== 'all', function ($query) use ($request) {
                        $query->whereHas('expense.sub_category', function ($query) use ($request) {
                            $query->where('id', $request->sub_category_id);
                        });
                    })
                    ->when($request->status && $request->status !== 'all', function ($query) use ($request) {
                        $query->where('status', $request->status);
                    })
                    ->when($request->to_date && $request->from_date, function ($query) use ($request) {
                        $fromDate = $request->from_date;
                        $toDate = $request->to_date;
                        if ($fromDate === $toDate) {
                            $toDate =Carbon::parse($toDate)->endOfDay();
                        }
                        $query->whereBetween('created_at', [$fromDate, $toDate]);
                    });
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
                            case 'issued':
                                $badge = '<span class="badge bg-secondary">Issued</span>';
                                break;
                            case 'reject':
                                $badge = '<span class="badge bg-danger">Rejected</span>';
                                break;
                            case 'passed':
                                $badge = '<span class="badge bg-success-1">Passed</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }
                        return $badge;
                    })
                    ->rawColumns(['status', 'company'])
                    ->make(true);
            }
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies =  auth()->user()->companies;
            }
            // $companies = auth()->user()->companies;
            $defaultCompany = auth()->user()->defaultCompany;
            return view('pages.report.payment.index', ['companies'=>$companies, 'defaultCompany' => $defaultCompany->id]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }
}
