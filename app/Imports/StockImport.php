<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StockImport
{
    /** Rows successfully imported */
    public int $importedCount = 0;

    /** Per-row error messages  [ ['row' => N, 'error' => '...'], ... ] */
    public array $errors = [];

    /**
     * Process the uploaded file.
     *
     * @param  string  $filePath  Absolute path to the temp-stored uploaded file
     * @param  int     $userId    Authenticated user id
     */
    public function import(string $filePath, int $userId): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false);

        // First row = headings → normalise to lowercase snake_case keys
        $headings = array_map(
            fn($h) => strtolower(trim(str_replace(' ', '_', (string) $h))),
            array_shift($rows) ?? []
        );

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-based, accounting for heading row

            // Skip completely empty rows
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            // Map heading keys → values
            $data = array_combine($headings, $row);

            // --- Field extraction ---
            $type      = strtolower(trim((string) ($data['type']      ?? '')));
            $itemCode  = strtoupper(trim((string) ($data['item_code'] ?? '')));
            $stockQty  = $data['stock_qty']    ?? null;
            $unitPrice = $data['unit_price']   ?? null;
            $remark    = trim((string) ($data['remark']    ?? ''));
            $dateRaw   = $data['date']         ?? null;

            // --- Validation ---
            if (!in_array($type, ['in', 'out'])) {
                $this->errors[] = ['row' => $rowNumber, 'error' => "Invalid Type '{$type}'. Must be 'in' or 'out'."];
                continue;
            }

            if ($itemCode === '') {
                $this->errors[] = ['row' => $rowNumber, 'error' => 'Item Code is empty.'];
                continue;
            }

            if (!is_numeric($stockQty) || (float) $stockQty < 0) {
                $this->errors[] = ['row' => $rowNumber, 'error' => "Stock Qty '{$stockQty}' is invalid."];
                continue;
            }

            if (!is_numeric($unitPrice) || (float) $unitPrice < 0) {
                $this->errors[] = ['row' => $rowNumber, 'error' => "Unit Price '{$unitPrice}' is invalid."];
                continue;
            }

            // --- Date parsing ---
            $stockDate = null;
            if ($dateRaw !== null && $dateRaw !== '') {
                // Handle Excel numeric date serial
                if (is_numeric($dateRaw)) {
                    try {
                        $stockDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw)
                            ->format('Y-m-d');
                    } catch (\Throwable) {
                        $stockDate = null;
                    }
                } else {
                    // Try common date formats
                    foreach (['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y'] as $fmt) {
                        $parsed = \DateTime::createFromFormat($fmt, trim((string) $dateRaw));
                        if ($parsed) {
                            $stockDate = $parsed->format('Y-m-d');
                            break;
                        }
                    }
                }
            }

            // --- Look up item ---
            $item = Item::where('item_code', $itemCode)->first();
            if (!$item) {
                $this->errors[] = ['row' => $rowNumber, 'error' => "Item Code '{$itemCode}' not found."];
                continue;
            }

            // --- Persist inside a transaction ---
            try {
                DB::beginTransaction();

                // Update item unit_price if changed
                if ((float) $item->unit_price !== (float) $unitPrice) {
                    $item->update([
                        'unit_price' => (float) $unitPrice,
                        'updated_by' => $userId,
                    ]);
                }

                Stock::create([
                    'item_id'          => $item->id,
                    'transaction_type' => $type,
                    'stock_quantity'   => (float) $stockQty,
                    'unit_price'       => (float) $unitPrice,
                    'remark'           => $remark ?: null,
                    'stock_date'       => $stockDate,
                    'created_by'       => $userId,
                ]);

                DB::commit();
                $this->importedCount++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->errors[] = ['row' => $rowNumber, 'error' => 'DB error: ' . $e->getMessage()];
            }
        }
    }
}
