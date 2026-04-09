@extends('layouts.main')

@section('title', 'Stock History — ' . $item->item_name)
@section('breadcrumb-item', 'Reports')
@section('breadcrumb-item-active', 'Stock History')

@section('content')
<!-- [ Main Content ] start -->

{{-- Item Info Card --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    @if($item->item_photo)
                        <img src="{{ asset('storage/' . $item->item_photo) }}" alt="Item Photo"
                             style="width:70px;height:70px;object-fit:cover;border-radius:10px;border:1px solid #dee2e6;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light"
                             style="width:70px;height:70px;border-radius:10px;border:1px dashed #ccc;">
                            <i class="ti ti-photo-off f-24 text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $item->item_name }}</h5>
                        <span class="text-muted small">
                            No: <strong>{{ $item->item_no }}</strong> &nbsp;|&nbsp;
                            Code: <strong>{{ $item->item_code }}</strong> &nbsp;|&nbsp;
                            Unit Price: <strong>LKR {{ number_format($item->unit_price, 2) }}</strong>
                        </span>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('admin.stock_report.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i> Back to Report
                        </a>
                        <a href="{{ route('admin.stocks.index') }}" class="btn btn-primary btn-sm ms-1">
                            <i class="ti ti-plus me-1"></i> Add Stock
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Summary row --}}
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
                <i class="ti ti-stack-2 f-28 text-success"></i>
                <h4 class="mt-1 mb-0" id="hist_total_qty">—</h4>
                <p class="text-muted mb-0 small">Total Stock Quantity</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
                <i class="ti ti-currency-dollar f-28 text-warning"></i>
                <h4 class="mt-1 mb-0" id="hist_total_value">—</h4>
                <p class="text-muted mb-0 small">Total Stock Value</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
                <i class="ti ti-list-numbers f-28 text-primary"></i>
                <h4 class="mt-1 mb-0" id="hist_entries">—</h4>
                <p class="text-muted mb-0 small">Total Stock Entries</p>
            </div>
        </div>
    </div>
</div>

{{-- History Table --}}
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-history me-2"></i>
                    Stock Entry History — {{ $item->item_name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm data-table" id="historyTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Stock Qty</th>
                                <th>Unit Price (LKR)</th>
                                <th>Line Value (LKR)</th>
                                <th>Remark</th>
                                <th>Added By</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td colspan="2" class="text-end">Total Balance</td>
                                <td id="foot_qty">0</td>
                                <td></td>
                                <td id="foot_value">0.00</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script>
$(function () {
    var table = $('#historyTable').DataTable({
        dom: '<"top"lBf>rt<"bottom"ip><"clear">',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.stock_report.history', $item->id) }}",
            dataSrc: function (json) {
                var totalQty   = 0;
                var totalValue = 0;

                json.data.forEach(function (row) {
                    var qty = parseFloat(row.stock_quantity) || 0;
                    var val = qty * parseFloat(row.unit_price || 0);

                    if (row.transaction_type === 'out') {
                        totalQty -= qty;
                        totalValue -= val;
                    } else {
                        totalQty += qty;
                        totalValue += val;
                    }
                });

                $('#foot_qty').text(totalQty.toFixed(2));
                $('#foot_value').text(totalValue.toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#hist_total_qty').text(totalQty.toFixed(2));
                $('#hist_total_value').text('LKR ' + totalValue.toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#hist_entries').text(json.recordsTotal ?? json.data.length);

                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex',        name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'transaction_type_badge', name: 'transaction_type', orderable: false },
            { data: 'stock_quantity_fmt',  name: 'stock_quantity' },
            { data: 'unit_price',          name: 'unit_price',
              render: d => parseFloat(d || 0).toFixed(2) },
            { data: 'line_value',          name: 'line_value', orderable: false },
            { data: 'remark',              name: 'remark', orderable: false,
              render: d => d || '—' },
            { data: 'created_by_name',     name: 'created_by_name', orderable: false },
            { data: 'created_at_fmt',      name: 'created_at', orderable: false },
        ],
        order: [[7, 'desc']],
    });
});
</script>
@endsection
