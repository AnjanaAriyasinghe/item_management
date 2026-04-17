<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $sale->sale_no }}</title>
    <style>
        /* ── Receipt Print Styles ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f7fa;
            color: #1a1a2e;
            padding: 20px;
        }

        .receipt-wrapper {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,.12);
            overflow: hidden;
        }

        /* Header */
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 32px 36px 24px;
            text-align: center;
            position: relative;
        }
        .receipt-header img.company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .receipt-header h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: .5px;
            margin-bottom: 2px;
        }
        .receipt-header p {
            font-size: 13px;
            opacity: .85;
            line-height: 1.5;
        }
        .receipt-badge {
            position: absolute;
            top: 24px;
            right: 28px;
            background: rgba(255,255,255,.18);
            border: 2px solid rgba(255,255,255,.4);
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        /* Meta row */
        .receipt-meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 36px;
            background: #f8f9fc;
            border-bottom: 2px solid #eef0f8;
            font-size: 13px;
        }
        .receipt-meta .meta-block { flex: 1; }
        .receipt-meta .meta-block .label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 3px;
        }
        .receipt-meta .meta-block .value {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 14px;
        }

        /* Items table */
        .receipt-body { padding: 24px 36px; }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table thead tr {
            background: linear-gradient(135deg,#667eea,#764ba2);
            color: #fff;
        }
        .items-table thead th {
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            text-align: left;
        }
        .items-table thead th.text-right,
        .items-table tbody td.text-right { text-align: right; }
        .items-table tbody tr {
            border-bottom: 1px solid #f0f1f8;
            transition: background .15s;
        }
        .items-table tbody tr:nth-child(even) { background: #fafbff; }
        .items-table tbody td {
            padding: 10px 14px;
            font-size: 14px;
            color: #333;
        }
        .items-table tbody td .item-code {
            font-size: 11px;
            color: #999;
            display: block;
        }

        /* Totals */
        .totals-section {
            max-width: 320px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            border-bottom: 1px solid #eef0f8;
            color: #444;
        }
        .totals-row .t-label { color: #777; }
        .totals-row .t-value { font-weight: 600; }
        .totals-row.grand {
            margin-top: 8px;
            padding: 12px 0;
            font-size: 20px;
            font-weight: 800;
            color: #667eea;
            border-bottom: none;
        }
        .totals-row.discount-row .t-value { color: #e74c3c; }

        /* Note */
        .receipt-note {
            margin: 16px 36px;
            background: #f8f9fc;
            border-left: 4px solid #667eea;
            padding: 10px 14px;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            color: #555;
        }

        /* Footer */
        .receipt-footer {
            text-align: center;
            padding: 20px 36px 28px;
            border-top: 2px dashed #e8eaf2;
            margin-top: 4px;
        }
        .receipt-footer p { font-size: 13px; color: #888; margin-bottom: 4px; }
        .receipt-footer .tagline {
            font-size: 15px;
            font-weight: 700;
            color: #667eea;
        }

        /* Print button (hidden when printing) */
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f5f7fa;
        }
        .btn-print {
            background: linear-gradient(135deg,#667eea,#764ba2);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px 32px;
            font-size: 15px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-close-tab {
            background: #f0f3ff;
            border: 2px solid #667eea;
            color: #667eea;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px 24px;
            font-size: 15px;
            cursor: pointer;
        }

        /* ── Print Media Query ── */
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-wrapper { box-shadow: none; border-radius: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">

    {{-- ── Header ── --}}
    <div class="receipt-header">
        @if($company && $company->logo)
            <img src="{{ asset('build/images/Logo/logo.png') }}" alt="Logo" class="company-logo">
        @endif
        <h1>YAOHANS</h1>
        <p>67/1 , Narahenpita Rd , Nawala</p>
        @if($company && $company->phone)
            <p>Tel: {{ $company->phone }}</p>
        @endif
        <div class="receipt-badge">RECEIPT</div>
    </div>

    {{-- ── Meta ── --}}
    <div class="receipt-meta">
        <div class="meta-block">
            <div class="label">Receipt No.</div>
            <div class="value">{{ $sale->sale_no }}</div>
        </div>
        <div class="meta-block">
            <div class="label">Date</div>
            <div class="value">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</div>
        </div>
        <div class="meta-block">
            <div class="label">Customer</div>
            <div class="value">
                @if($sale->customer)
                    {{ $sale->customer->name }}
                    @if($sale->customer->phone)
                        <br><small style="font-weight:400;color:#888">{{ $sale->customer->phone }}</small>
                    @endif
                @else
                    Walk-in Customer
                @endif
            </div>
        </div>
        <div class="meta-block">
            <div class="label">Served By</div>
            <div class="value">{{ $sale->createdBy?->name ?? '—' }}</div>
        </div>
    </div>

    {{-- ── Items ── --}}
    <div class="receipt-body">
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $idx => $line)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        {{ $line->item_name }}
                        <span class="item-code">{{ $line->item_code }}</span>
                    </td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($line->quantity, 2), '0'), '.') }}</td>
                    <td class="text-right">Rs. {{ number_format($line->unit_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($line->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <div class="totals-row">
                <span class="t-label">Subtotal</span>
                <span class="t-value">Rs. {{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
            <div class="totals-row discount-row">
                <span class="t-label">
                    Discount
                    @if($sale->discount_type === 'percent')
                        ({{ $sale->discount_value }}%)
                    @else
                        (Fixed)
                    @endif
                </span>
                <span class="t-value">− Rs. {{ number_format($sale->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="totals-row grand">
                <span>Total</span>
                <span>Rs. {{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Note --}}
    @if($sale->note)
    <div class="receipt-note">
        <strong>Note:</strong> {{ $sale->note }}
    </div>
    @endif

    {{-- Footer --}}
    <div class="receipt-footer">
        <p class="tagline">Thank you for your purchase!</p>
        <p>Please retain this receipt for your records.</p>
        <p style="margin-top:12px;font-size:12px;">
            {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp; {{ $sale->sale_no }}
        </p>
    </div>
</div>

{{-- Print / Close buttons (hidden on print) --}}
<div class="no-print" style="margin-top: 20px;">
    <button class="btn-print" onclick="window.print()">
        🖨&nbsp; Print Receipt
    </button>
    <button class="btn-close-tab" onclick="window.close()">
        ✕&nbsp; Close
    </button>
</div>

<script>
    // Auto-print when this page loads in a new tab
    window.addEventListener('load', function () {
        // Small delay so styles render fully
        setTimeout(function () {
            window.print();
        }, 400);
    });
</script>
</body>
</html>
