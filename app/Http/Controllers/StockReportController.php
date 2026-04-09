<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Builder;

class StockReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Item-wise stock summary (total quantity per item).
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Item::select('id', 'item_no', 'item_code', 'item_name', 'unit_price')
                    ->withSum(['stocks as total_in' => function(Builder $query) {
                        $query->where('transaction_type', 'in');
                    }], 'stock_quantity')
                    ->withSum(['stocks as total_out' => function(Builder $query) {
                        $query->where('transaction_type', 'out');
                    }], 'stock_quantity');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('total_quantity', function ($row) {
                        $in  = $row->total_in ?? 0;
                        $out = $row->total_out ?? 0;
                        return $in - $out;
                    })
                    ->addColumn('total_value', function ($row) {
                        $in  = $row->total_in ?? 0;
                        $out = $row->total_out ?? 0;
                        $qty = $in - $out;
                        $price = $row->unit_price ?? 0;
                        return number_format($qty * $price, 2);
                    })
                    ->addColumn('stock_status', function ($row) {
                        $in  = $row->total_in ?? 0;
                        $out = $row->total_out ?? 0;
                        $qty = $in - $out;
                        if ($qty <= 0) {
                            return '<span class="badge bg-danger">Out of Stock</span>';
                        } elseif ($qty <= 10) {
                            return '<span class="badge bg-warning text-dark">Low Stock</span>';
                        }
                        return '<span class="badge bg-success">In Stock</span>';
                    })
                    ->addColumn('action', function ($row) {
                        if (!auth()->user()->can('admin-common-stocks-report-view')) {
                            return '';
                        }
                        return '<a href="' . route('admin.stock_report.history', $row->id) . '"
                                   class="btn btn-info btn-sm">
                                   <i class="ti ti-history f-20"></i> History
                               </a>';
                    })
                    ->rawColumns(['stock_status', 'action'])
                    ->make(true);
            }

            return view('pages.admin.stock_report.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Stock history for a specific item.
     */
    public function history(Request $request, $itemId)
    {
        try {
            $item = Item::findOrFail($itemId);

            if ($request->ajax()) {
                $data = Stock::with('created_user')
                    ->where('item_id', $itemId)
                    ->select('stocks.*')
                    ->latest('id');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('transaction_type_badge', function($row) {
                        return $row->transaction_type === 'in' 
                            ? '<span class="badge bg-success">Stock In</span>'
                            : '<span class="badge bg-danger">Stock Out</span>';
                    })
                    ->addColumn('created_by_name', fn($row) => $row->created_user->name ?? '—')
                    ->addColumn('created_at_fmt', fn($row) => $row->created_at?->format('Y-m-d H:i'))
                    ->addColumn('stock_quantity_fmt', function($row) {
                        $sign = $row->transaction_type === 'out' ? '-' : '';
                        $col  = $row->transaction_type === 'out' ? 'text-danger' : 'text-success';
                        return '<span class="fw-bold ' . $col . '">' . $sign . number_format($row->stock_quantity, 2) . '</span>';
                    })
                    ->addColumn('line_value', function($row) {
                        $val = $row->stock_quantity * $row->unit_price;
                        $sign = $row->transaction_type === 'out' ? '-' : '';
                        $col  = $row->transaction_type === 'out' ? 'text-danger' : 'text-success';
                        return '<span class="fw-bold ' . $col . '">' . $sign . number_format($val, 2) . '</span>';
                    })
                    ->rawColumns(['transaction_type_badge', 'stock_quantity_fmt', 'line_value'])
                    ->make(true);
            }

            return view('pages.admin.stock_report.history', compact('item'));
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
