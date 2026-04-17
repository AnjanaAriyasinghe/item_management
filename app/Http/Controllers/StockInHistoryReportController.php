<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockInHistoryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Stock::with(['item', 'created_user'])
                ->select('stocks.*')
                ->where('transaction_type', 'in');

            // Filters
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            if ($request->filled('item_id')) {
                $query->where('item_id', $request->item_id);
            }

            // Optional totals for footer
            $summaryQuery = clone $query;
            $totals = [
                'total_qty' => $summaryQuery->sum('stock_quantity'),
                'total_value'  => (clone $query)->select(DB::raw('SUM(stock_quantity * unit_price) as total'))->value('total') ?? 0,
            ];

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('stock_date_fmt', function($row) {
                    return $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d') : 'N/A';
                })
                ->addColumn('item_code', function($row) {
                    return $row->item ? $row->item->item_code : 'N/A';
                })
                ->addColumn('item_name', function($row) {
                    return $row->item ? $row->item->item_name : 'N/A';
                })
                ->addColumn('unit_price_fmt', function($row) {
                    return number_format($row->unit_price, 2);
                })
                ->addColumn('stock_quantity_fmt', function($row) {
                    return number_format($row->stock_quantity, 2);
                })
                ->addColumn('total_value_fmt', function($row) {
                    return number_format($row->stock_quantity * $row->unit_price, 2);
                })
                ->addColumn('created_by_name', function($row) {
                    return $row->created_user ? $row->created_user->name : 'System';
                })
                ->with('totals', $totals)
                ->rawColumns([])
                ->make(true);
        }

        $items = Item::select('id', 'item_code', 'item_name')->orderBy('item_name')->get();
        return view('pages.admin.stock_in_history_report.index', compact('items'));
    }
}
