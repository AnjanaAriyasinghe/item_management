<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\ChequeBook;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Payment;
use App\Models\Vendor;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    // public function pageView($routeName, $page = null)
    // {
    //     // Construct the view name based on the provided routeName and optional page parameter
    //     $viewName = ($page) ? $routeName.'.'.$page : $routeName;
    //     // Check if the constructed view exists
    //     if (\View::exists($viewName)) {
    //         // If the view exists, return the view
    //         return view($viewName);
    //     } else {
    //         // If the view doesn't exist, return a 404 error
    //         abort(404);
    //     }
    // }

    public function dashboard(Request $request)
    {
        try {
            if ($request->ajax()) {
                // $data = Payment::with('expense.category', 'expense.sub_category', 'vendor','company')
                //     ->latest('id')
                //     ->take(5)
                //     ->get();

                 // $query = Payment::with('expense.category', 'expense.sub_category', 'vendor','company')
                // ->latest('id');

                //     $query->when($request->company_id != 'all', function ($query) use ($request) {
                //         $query->whereHas('company', function ($query) use ($request) {
                //             $query->where('companies.id', $request->company_id);
                //         });
                // });

                // $data = $query->latest('id')->take(5)->get();


                if ($request->company_id) {
                    $data = Payment::with('expense.category', 'expense.sub_category', 'vendor','company')
                    ->latest('id')

                    ->when($request->company_id != 'all', function ($query) use ($request) {
                            $query->whereHas('company', function ($query) use ($request) {
                                $query->where('companies.id', $request->company_id);
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
                    ->rawColumns(['status','company'])
                    ->make(true);
            }
            $months = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ];
            $currentMonth = date('F');
             if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies =  auth()->user()->companies;
            }
            $defaultCompany = auth()->user()->defaultCompany;

            return view('dashboard', ['months' => $months, 'currentMonth' => $currentMonth, 'companies' => $companies, 'defaultCompany' => $defaultCompany->id]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    public function dashboard_data()
    {
        try {
            $expens_categories = ExpenseCategory::count();
            $expens_sub_categories = ExpenseSubCategory::count();
            $venndors = Vendor::count();
            $bankAccounts = BankAccount::count();
            $cheque_books = ChequeBook::count();
            $data = [
                'expens_categories' => $expens_categories,
                'expens_sub_categories' => $expens_sub_categories,
                'venndors' => $venndors,
                'bankAccounts' => $bankAccounts,
                'cheque_books' => $cheque_books,
            ];
            return response()->json(['data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function expenses_chart(Request $request)
    {
        try {
            $company_id = $request->input('company_id');
            $year = $request->input('year');
            $selector = $request->input('selector');
            $monthly = $request->input('monthly');
            $startDate = null;
            $endDate = null;

            if ($selector === 'monthly' && $monthly) {
                $startDate = date('Y-m-d', strtotime("first day of $monthly $year"));
                $endDate = date('Y-m-d', strtotime("last day of $monthly $year"));
            } elseif ($selector === 'daily') {
                $startDate =  $request->input('daily');
                $endDate =  $request->input('daily');
            }

            if ($company_id) {

                $expenses =  Expense::with('category')
                ->where('status','approved')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->select(DB::raw('expense_categories.name as category, SUM(amount) as total'))
                ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                ->groupBy('expense_categories.name')
                ->when($request->company_id != 'all', function ($query) use ($request) {
                        $query->whereHas('company', function ($query) use ($request) {
                            $query->where('id', $request->company_id);
                        });
                });

            }
            // $expenses = Expense::with('category')
            //     ->where('status','approved')
            //     ->whereBetween('expense_date', [$startDate, $endDate])
            //     ->select(DB::raw('expense_categories.name as category, SUM(amount) as total'))
            //     ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            //     ->groupBy('expense_categories.name')
            //     ->get();


            $colors = [];
            $categoryCount = $expenses->pluck('category')->count(); // Get the number of categories
            for ($i = 0; $i < $categoryCount; $i++) {
                $colors[] = $this->generateRandomColor();
            }
            $chartData = [
                'labels' => $expenses->pluck('category'),
                'data' => $expenses->pluck('total'),
                'colors' => $colors,
            ];

            return response()->json($chartData);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function payment_chart(Request $request)
    {
        try {
            $company_id = $request->input('company_id');
            $year = $request->input('year');
            $selector = $request->input('selector');
            $monthly = $request->input('monthly');
            $startDate = null;
            $endDate = null;

            if ($selector === 'monthly' && $monthly) {
                $startDate = date('Y-m-d', strtotime("first day of $monthly $year"));
                $endDate = date('Y-m-d', strtotime("last day of $monthly $year"));
            } elseif ($selector === 'daily') {
                $startDate =  $request->input('daily');
                $endDate =  $request->input('daily');
            }

            if ($company_id) {
                $payments = Payment::with('expense.category')
                ->join('expenses', 'payments.expense_id', '=', 'expenses.id') // Join with expenses
                ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id') // Join with categories
                ->whereBetween('payment_date', [$startDate, $endDate]) // Correct the field for expense_date
                ->select(DB::raw('expense_categories.name as category, SUM(payments.amount) as total')) // Correct column references
                ->groupBy('expense_categories.name')
                ->when($company_id != 'all', function ($query) use ($company_id) {
                        $query->whereHas('company', function ($query) use ($company_id) {
                            $query->where('id', $company_id);
                            // $query->where('id', $request->company_id);
                        });
                });
            }


            $colors = [];
            $categoryCount = $payments->pluck('category')->count(); // Get the number of categories
            for ($i = 0; $i < $categoryCount; $i++) {
                $colors[] = $this->generateRandomColor();
            }
            $chartData = [
                'labels' => $payments->pluck('category'),
                'data' => $payments->pluck('total'),
                'colors' => $colors,
            ];

            return response()->json($chartData);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    function generateRandomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
