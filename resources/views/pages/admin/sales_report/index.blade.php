@extends('layouts.main')

@section('title', 'Sales Report')
@section('breadcrumb-item', 'Reports')
@section('breadcrumb-item-active', 'Sales Report')

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
    .summary-card.gross { background: linear-gradient(135deg, #1f3b73, #3a60b5); }
    .summary-card.discount { background: linear-gradient(135deg, #b55b25, #e08044); }
    .summary-card.net { background: linear-gradient(135deg, #1b6343, #2f9c6e); }
    
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
                            <input type="date" class="form-control" name="from_date" id="from_date" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Customer</label>
                            <select class="form-select" name="customer_id" id="customer_id">
                                <option value="">All Customers</option>
                                <option value="walk-in">Walk-in Customers Only</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
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
    <div class="col-md-4">
        <div class="summary-card gross">
            <div>
                <p class="title">Total Subtotal</p>
                <p class="value" id="card-subtotal">Rs. 0.00</p>
            </div>
            <div class="icon"><i class="ph-duotone ph-currency-circle-dollar"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card discount">
            <div>
                <p class="title">Total Discounts Given</p>
                <p class="value" id="card-discount">Rs. 0.00</p>
            </div>
            <div class="icon"><i class="ph-duotone ph-tag"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card net">
            <div>
                <p class="title">Net Grand Total</p>
                <p class="value" id="card-total">Rs. 0.00</p>
            </div>
            <div class="icon"><i class="ph-duotone ph-wallet"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-report-money me-2"></i>Sales Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm data-table" id="sales-report-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Sale No</th>
                                <th>Customer</th>
                                <th class="text-end">Subtotal (Rs.)</th>
                                <th class="text-end">Discount (Rs.)</th>
                                <th class="text-end fw-bold">Grand Total (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-end" id="foot-subtotal">0.00</th>
                                <th class="text-end" id="foot-discount">0.00</th>
                                <th class="text-end fw-bold" id="foot-total">0.00</th>
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
    var table = $('#sales-report-table').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"lBf>rt<"mt-3"ip><"clear">',
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm me-1', text: '<i class="ti ti-file-spreadsheet"></i> Excel' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm me-1', text: '<i class="ti ti-file-text"></i> PDF' },
            { extend: 'print', className: 'btn btn-info btn-sm', text: '<i class="ti ti-printer"></i> Print' }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.sales_report.index') }}",
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.customer_id = $('#customer_id').val();
            }
        },
        order: [[1, 'desc']],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'sale_date_fmt', name: 'sale_date' },
            { data: 'sale_no', name: 'sale_no' },
            { data: 'customer_name', name: 'customer_name', orderable: false },
            { data: 'subtotal_fmt', name: 'subtotal', className: 'text-end' },
            { data: 'discount_amount_fmt', name: 'discount_amount', className: 'text-end text-warning' },
            { data: 'total_amount_fmt', name: 'total_amount', className: 'text-end fw-bold text-success' },
        ],
        drawCallback: function(settings) {
            var api = this.api();
            if (settings.json && settings.json.totals) {
                var t = settings.json.totals;
                $('#card-subtotal, #foot-subtotal').text(Number(t.total_subtotal).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#card-discount, #foot-discount').text(Number(t.total_discount).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#card-total, #foot-total').text('Rs. ' + Number(t.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#foot-total').text(Number(t.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2}));
            }
        }
    });

    $('#btnFilter').click(function() {
        table.draw();
    });
});
</script>
@endsection
