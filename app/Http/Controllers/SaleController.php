<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of all sales / receipts.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Sale::with(['customer', 'createdBy'])->latest('id');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('customer_name', fn($row) => $row->customer?->name ?? '<span class="badge bg-secondary">Walk-in</span>')
                    ->addColumn('total_amount_fmt', fn($row) => 'Rs. ' . number_format($row->total_amount, 2))
                    ->addColumn('sale_date_fmt', fn($row) => \Carbon\Carbon::parse($row->sale_date)->format('d M Y'))
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        $buttons .= '<a href="' . route('admin.sales.edit', $data->id) . '" class="btn btn-warning btn-sm me-1" title="Edit Sale">
                                        <i class="ti ti-pencil f-20"></i>
                                    </a>';
                        $buttons .= '<button class="btn btn-info btn-sm me-1 btnSaleView" data-id="' . $data->id . '" title="View Details">
                                        <i class="ti ti-eye f-20"></i>
                                    </button>';
                        $buttons .= '<a href="' . route('admin.sales.show', $data->id) . '" target="_blank"
                                        class="btn btn-success btn-sm me-1" title="Print Receipt">
                                        <i class="ti ti-printer f-20"></i>
                                    </a>';
                        $buttons .= '<button class="btn btn-danger btn-sm"
                                        onclick="handleDelete(\'' . route('admin.sales.destroy', $data->id) . '\', { _token: \'' . csrf_token() . '\' })"
                                        title="Delete">
                                        <i class="ti ti-trash f-20"></i>
                                    </button>';
                        return $buttons;
                    })
                    ->rawColumns(['customer_name', 'action'])
                    ->make(true);
            }
            return view('pages.admin.sales.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Show the form for creating a new sale / receipt.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'customer_code', 'name', 'phone']);
        return view('pages.admin.sales.create', compact('customers'));
    }

    /**
     * Store a newly created sale and its line items.
     * Also records a Stock Out entry per line item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_date'        => 'required|date',
            'customer_id'      => 'nullable|exists:customers,id',
            'discount_type'    => 'required|in:percent,fixed',
            'discount_value'   => 'required|numeric|min:0',
            'note'             => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.item_id'    => 'required|exists:items,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $line) {
                $subtotal += $line['quantity'] * $line['unit_price'];
            }

            $discountAmount = 0;
            if ($request->discount_type === 'percent') {
                $discountAmount = $subtotal * ($request->discount_value / 100);
            } else {
                $discountAmount = min($request->discount_value, $subtotal);
            }
            $total = $subtotal - $discountAmount;

            // Create Sale header
            $sale = Sale::create([
                'customer_id'    => $request->customer_id ?: null,
                'sale_date'      => $request->sale_date,
                'subtotal'       => $subtotal,
                'discount_type'  => $request->discount_type,
                'discount_value' => $request->discount_value,
                'discount_amount'=> $discountAmount,
                'total_amount'   => $total,
                'note'           => $request->note,
                'created_by'     => auth()->id(),
            ]);

            // Create line items + Stock Out entries
            foreach ($request->items as $line) {
                $item = Item::findOrFail($line['item_id']);
                $lineTotal = $line['quantity'] * $line['unit_price'];

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'item_id'    => $item->id,
                    'item_name'  => $item->item_name,
                    'item_code'  => $item->item_code,
                    'quantity'   => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $lineTotal,
                ]);

                // Record Stock Out
                Stock::create([
                    'item_id'          => $item->id,
                    'transaction_type' => 'out',
                    'stock_quantity'   => $line['quantity'],
                    'unit_price'       => $line['unit_price'],
                    'remark'           => 'Sale: ' . $sale->sale_no,
                    'stock_date'       => $request->sale_date,
                    'created_by'       => auth()->id(),
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Sale saved successfully.',
                'status'  => true,
                'sale_id' => $sale->id,
                'print_url' => route('admin.sales.show', $sale->id),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Show the form for editing the specified sale / receipt.
     */
    public function edit(Sale $sale)
    {
        $sale->load(['saleItems.item']);
        $customers = Customer::orderBy('name')->get(['id', 'customer_code', 'name', 'phone']);
        return view('pages.admin.sales.edit', compact('sale', 'customers'));
    }

    /**
     * Update the specified sale and its line items.
     * Also manages Stock Out entry reversal and new records.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'sale_date'        => 'required|date',
            'customer_id'      => 'nullable|exists:customers,id',
            'discount_type'    => 'required|in:percent,fixed',
            'discount_value'   => 'required|numeric|min:0',
            'note'             => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.item_id'    => 'required|exists:items,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Reverse old stock transactions related to this sale
            Stock::where('remark', 'Sale: ' . $sale->sale_no)->forceDelete();

            // Delete old sale items
            $sale->saleItems()->delete();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $line) {
                $subtotal += $line['quantity'] * $line['unit_price'];
            }

            $discountAmount = 0;
            if ($request->discount_type === 'percent') {
                $discountAmount = $subtotal * ($request->discount_value / 100);
            } else {
                $discountAmount = min($request->discount_value, $subtotal);
            }
            $total = $subtotal - $discountAmount;

            // Update Sale header
            $sale->update([
                'customer_id'    => $request->customer_id ?: null,
                'sale_date'      => $request->sale_date,
                'subtotal'       => $subtotal,
                'discount_type'  => $request->discount_type,
                'discount_value' => $request->discount_value,
                'discount_amount'=> $discountAmount,
                'total_amount'   => $total,
                'note'           => $request->note,
                'updated_by'     => auth()->id(),
            ]);

            // Re-create line items + Stock Out entries
            foreach ($request->items as $line) {
                $item = Item::findOrFail($line['item_id']);
                $lineTotal = $line['quantity'] * $line['unit_price'];

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'item_id'    => $item->id,
                    'item_name'  => $item->item_name,
                    'item_code'  => $item->item_code,
                    'quantity'   => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $lineTotal,
                ]);

                // Record new Stock Out
                Stock::create([
                    'item_id'          => $item->id,
                    'transaction_type' => 'out',
                    'stock_quantity'   => $line['quantity'],
                    'unit_price'       => $line['unit_price'],
                    'remark'           => 'Sale: ' . $sale->sale_no,
                    'stock_date'       => $request->sale_date,
                    'created_by'       => auth()->id(), // Keeps track of who updated it via this transaction
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Sale updated successfully.',
                'status'  => true,
                'sale_id' => $sale->id,
                'print_url' => route('admin.sales.show', $sale->id),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Return sale details as JSON for the view modal.
     */
    public function getDetail(Sale $sale)
    {
        try {
            $sale->load(['customer', 'saleItems', 'createdBy']);
            return response()->json([
                'status' => true,
                'sale'   => [
                    'sale_no'         => $sale->sale_no,
                    'sale_date'       => \Carbon\Carbon::parse($sale->sale_date)->format('d M Y'),
                    'customer_name'   => $sale->customer?->name ?? 'Walk-in Customer',
                    'customer_code'   => $sale->customer?->customer_code ?? '—',
                    'customer_phone'  => $sale->customer?->phone ?? '—',
                    'customer_city'   => $sale->customer?->city ?? '—',
                    'created_by'      => $sale->createdBy?->name ?? '—',
                    'subtotal'        => number_format($sale->subtotal, 2),
                    'discount_type'   => $sale->discount_type,
                    'discount_value'  => $sale->discount_value,
                    'discount_amount' => number_format($sale->discount_amount, 2),
                    'total_amount'    => number_format($sale->total_amount, 2),
                    'note'            => $sale->note,
                    'items'           => $sale->saleItems->map(fn($i) => [
                        'item_code'  => $i->item_code,
                        'item_name'  => $i->item_name,
                        'quantity'   => rtrim(rtrim(number_format($i->quantity, 2), '0'), '.'),
                        'unit_price' => number_format($i->unit_price, 2),
                        'line_total' => number_format($i->line_total, 2),
                    ]),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Show / print receipt for a given sale.
     */
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'saleItems', 'createdBy']);
        $company = \App\Models\Company::first();
        return view('pages.admin.sales.print', compact('sale', 'company'));
    }

    /**
     * Search items for the sale line-item Select2 dropdown.
     * Returns item details including current available stock.
     */
    public function searchItems(Request $request)
    {
        $term = $request->get('term', '');
        $items = Item::where(function ($q) use ($term) {
                $q->where('item_name', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%")
                  ->orWhere('item_no',   'like', "%{$term}%");
            })
            ->select('id', 'item_no', 'item_code', 'item_name', 'unit_price', 'item_description')
            ->limit(30)
            ->get();

        $results = $items->map(function ($i) {
            $available = $i->availableStock();
            return [
                'id'               => $i->id,
                'text'             => $i->item_name . ' (' . $i->item_code . ')',
                'item_no'          => $i->item_no,
                'item_code'        => $i->item_code,
                'item_name'        => $i->item_name,
                'unit_price'       => $i->unit_price,
                'item_description' => $i->item_description,
                'available_stock'  => $available,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Soft-delete a sale.
     */
    public function destroy(Sale $sale)
    {
        try {
            $sale->update(['deleted_by' => auth()->id()]);
            $sale->delete();
            return response()->json(['message' => 'Sale deleted successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
