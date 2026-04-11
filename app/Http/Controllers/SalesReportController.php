<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Sale::with(['customer', 'createdBy'])->select('sales.*');

            // Filters
            if ($request->filled('from_date')) {
                $query->whereDate('sale_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('sale_date', '<=', $request->to_date);
            }
            if ($request->filled('customer_id')) {
                // For "walk-in" filtering, we use 'walk-in' as the value
                if ($request->customer_id === 'walk-in') {
                    $query->whereNull('customer_id');
                } else {
                    $query->where('customer_id', $request->customer_id);
                }
            }

            // Calculations for the footer summary (only matching filtered records)
            $summaryQuery = clone $query;
            $totals = [
                'total_subtotal' => $summaryQuery->sum('subtotal'),
                'total_discount' => $summaryQuery->sum('discount_amount'),
                'total_amount'   => $summaryQuery->sum('total_amount')
            ];

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('sale_date_fmt', function($row) {
                    return Carbon::parse($row->sale_date)->format('Y-m-d');
                })
                ->addColumn('customer_name', function($row) {
                    return $row->customer ? $row->customer->name : '<span class="badge bg-secondary">Walk-in</span>';
                })
                ->addColumn('subtotal_fmt', function($row) {
                    return number_format($row->subtotal, 2);
                })
                ->addColumn('discount_amount_fmt', function($row) {
                    return number_format($row->discount_amount, 2);
                })
                ->addColumn('total_amount_fmt', function($row) {
                    return number_format($row->total_amount, 2);
                })
                ->with('totals', $totals) 
                ->rawColumns(['customer_name'])
                ->make(true);
        }

        $customers = Customer::orderBy('name')->get();
        return view('pages.admin.sales_report.index', compact('customers'));
    }
}
