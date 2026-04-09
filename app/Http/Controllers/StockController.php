<?php

namespace App\Http\Controllers;

use App\Imports\StockImport;
use App\Models\Item;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\DataTables;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Stock::with(['item', 'created_user']);
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('item_no', fn($row) => $row->item->item_no ?? '—')
                    ->addColumn('item_code', fn($row) => $row->item->item_code ?? '—')
                    ->addColumn('item_name', fn($row) => $row->item->item_name ?? '—')
                    ->addColumn('transaction_type_badge', function ($row) {
                        return $row->transaction_type === 'out'
                            ? '<span class="badge bg-danger">Stock Out</span>'
                            : '<span class="badge bg-success">Stock In</span>';
                    })
                    ->addColumn('created_at_fmt', fn($row) => $row->created_at?->format('Y-m-d'))
                    ->addColumn('stock_date', fn($row) => $row->stock_date ?? null)
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-stocks-view')) {
                            $buttons .= '<button class="btn btn-success btn-sm btnStockView me-1" data-id=\'' . $data->id . '\'><i class="ti ti-eye f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-stocks-edit')) {
                            $buttons .= '<button class="btn btn-warning btn-sm btnStockEdit me-1" data-id=\'' . $data->id . '\'><i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-stocks-delete')) {
                            $buttons .= '<button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.stocks.destroy', $data->id) . '\', { _token: \'' . csrf_token() . '\' })"><i class="ti ti-trash f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['transaction_type_badge', 'action'])
                    ->make(true);
            }
            return view('pages.admin.stock.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Search items for Select2 autocomplete.
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

        $results = $items->map(fn($i) => [
            'id'               => $i->id,
            'text'             => $i->item_name . ' (' . $i->item_code . ')',
            'item_no'          => $i->item_no,
            'item_code'        => $i->item_code,
            'item_name'        => $i->item_name,
            'unit_price'       => $i->unit_price,
            'item_description' => $i->item_description,
        ]);

        return response()->json(['results' => $results]);
    }

    /**
     * Store a newly created stock entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'          => 'required|exists:items,id',
            'transaction_type' => 'required|in:in,out',
            'stock_quantity'   => 'required|numeric|min:0',
            'unit_price'       => 'required|numeric|min:0',
            'remark'           => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Update item unit_price if it was changed
            $item = Item::findOrFail($request->item_id);
            if ((float)$item->unit_price !== (float)$request->unit_price) {
                $item->update([
                    'unit_price'  => $request->unit_price,
                    'updated_by'  => auth()->id(),
                ]);
            }

            $stock = Stock::create([
                'item_id'          => $request->item_id,
                'transaction_type' => $request->transaction_type,
                'stock_quantity'   => $request->stock_quantity,
                'unit_price'       => $request->unit_price,
                'remark'           => $request->remark,
                'created_by'       => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Stock entry added successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Return stock data for edit modal.
     */
    public function edit(Stock $stock)
    {
        try {
            $stock->load('item', 'created_user');
            return response()->json([
                'stock'        => $stock,
                'item'         => $stock->item,
                'select_text'  => $stock->item->item_name . ' (' . $stock->item->item_code . ')',
                'created_name' => $stock->created_user->name ?? '—',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified stock entry.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_id'          => 'required|exists:items,id',
            'transaction_type' => 'required|in:in,out',
            'stock_quantity'   => 'required|numeric|min:0',
            'unit_price'       => 'required|numeric|min:0',
            'remark'           => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $stock = Stock::findOrFail($id);

            // Update item unit_price if it changed
            $item = Item::findOrFail($request->item_id);
            if ((float)$item->unit_price !== (float)$request->unit_price) {
                $item->update([
                    'unit_price' => $request->unit_price,
                    'updated_by' => auth()->id(),
                ]);
            }

            $stock->update([
                'item_id'          => $request->item_id,
                'transaction_type' => $request->transaction_type,
                'stock_quantity'   => $request->stock_quantity,
                'unit_price'       => $request->unit_price,
                'remark'           => $request->remark,
                'updated_by'       => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Stock entry updated successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        try {
            $stock->update(['deleted_by' => auth()->id()]);
            $stock->delete();
            return response()->json(['message' => 'Stock entry deleted successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Bulk import stock entries from an uploaded Excel / CSV file.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $path = $request->file('excel_file')->store('stock_imports', 'local');
            $fullPath = storage_path('app/' . $path);

            $importer = new StockImport();
            $importer->import($fullPath, auth()->id());

            // Clean up temp file
            @unlink($fullPath);

            return response()->json([
                'status'         => true,
                'imported_count' => $importer->importedCount,
                'errors'         => $importer->errors,
                'message'        => $importer->importedCount . ' row(s) imported successfully.'
                    . (count($importer->errors) ? ' ' . count($importer->errors) . ' row(s) had errors.' : ''),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Download a blank Excel template for bulk stock import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Import');

        // Headers — PhpSpreadsheet 2.x uses coordinate strings
        $headers = ['Type', 'Item Code', 'Item Name', 'Stock Qty', 'Unit Price', 'Remark', 'Date'];
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $cell = $colLetter . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Example rows
        $examples = [
            ['in',  'ITM-001', 'Example Item A', 10, 150.00, 'Initial stock',  date('Y-m-d')],
            ['out', 'ITM-002', 'Example Item B',  5, 200.00, 'Issued to dept', date('Y-m-d')],
        ];
        foreach ($examples as $rIdx => $row) {
            foreach ($row as $cIdx => $val) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx + 1);
                $sheet->setCellValue($colLetter . ($rIdx + 2), $val);
            }
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'stock_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
