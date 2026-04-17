@extends('layouts.main')

@section('title', 'Add Item to Stock History')
@section('breadcrumb-item', 'Reports')
@section('breadcrumb-item-active', 'Add Item to Stock History')

@section('css')
<style>
    .summary-card {
        border-radius: 12px;
        color: #fff;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .summary-card.qty { background: linear-gradient(135deg, #1f3b73, #3a60b5); }
    .summary-card.value { background: linear-gradient(135deg, #1b6343, #2f9c6e); }

    .summary-card .title { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.85; margin-bottom: 5px; }
    .summary-card .value { font-size: 26px; font-weight: 700; margin: 0; }
    .summary-card .icon { font-size: 40px; opacity: 0.3; }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="date" class="form-control" name="from_date" id="from_date" >
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date" class="form-control" name="to_date" id="to_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Item</label>
                            <select class="form-select" name="item_id" id="item_id">
                                <option value="">All Items</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->item_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="btnFilter">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="summary-card qty">
            <div>
                <p class="title">Total Quantity Added</p>
                <p class="value" id="card-qty">0.00</p>
            </div>
            <div class="icon"><i class="ph-duotone ph-packages"></i></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="summary-card value">
            <div>
                <p class="title">Total Added Stock Value</p>
                <p class="value" id="card-value">Rs. 0.00</p>
            </div>
            <div class="icon"><i class="ph-duotone ph-currency-dollar"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-report me-2"></i>Stock In History Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm data-table" id="stock-in-history-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Added By</th>
                                <th class="text-end">Unit Price (Rs.)</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end fw-bold">Line Total (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">Total:</th>
                                <th class="text-end text-success fw-bold" id="foot-qty">0.00</th>
                                <th class="text-end fw-bold" id="foot-value">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#stock-in-history-table').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"lBf>rt<"mt-3"ip><"clear">',
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm me-1', text: '<i class="ti ti-file-spreadsheet"></i> Excel' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm me-1', text: '<i class="ti ti-file-text"></i> PDF' },
            { extend: 'print', className: 'btn btn-info btn-sm', text: '<i class="ti ti-printer"></i> Print' }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.stock_in_history_report.index') }}",
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.item_id = $('#item_id').val();
            }
        },
        order: [[1, 'desc']],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'stock_date_fmt', name: 'stock_date' },
            { data: 'item_code', name: 'item_code', orderable: false },
            { data: 'item_name', name: 'item_name', orderable: false },
            { data: 'created_by_name', name: 'created_by_name', orderable: false },
            { data: 'unit_price_fmt', name: 'unit_price', className: 'text-end' },
            { data: 'stock_quantity_fmt', name: 'stock_quantity', className: 'text-end text-success fw-bold' },
            { data: 'total_value_fmt', name: 'total_value', className: 'text-end fw-bold' },
        ],
        drawCallback: function(settings) {
            var api = this.api();
            if (settings.json && settings.json.totals) {
                var t = settings.json.totals;
                $('#card-qty, #foot-qty').text(Number(t.total_qty).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#card-value').text('Rs. ' + Number(t.total_value).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#foot-value').text(Number(t.total_value).toLocaleString('en-US', {minimumFractionDigits: 2}));
            }
        }
    });

    $('#btnFilter').click(function() {
        table.draw();
    });
});
</script>
@endsection
