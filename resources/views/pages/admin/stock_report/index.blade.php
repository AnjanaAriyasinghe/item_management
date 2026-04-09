@extends('layouts.main')

@section('title', 'Stock Report')
@section('breadcrumb-item', 'Reports')
@section('breadcrumb-item-active', 'Stock Report')

@section('content')
<!-- [ Main Content ] start -->

<div class="row mb-3">
    {{-- Summary Cards --}}
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
                <i class="ti ti-packages f-30 text-primary"></i>
                <h4 class="mt-1 mb-0" id="card_total_items">—</h4>
                <p class="text-muted mb-0 small">Total Items</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
                <i class="ti ti-stack-2 f-30 text-success"></i>
                <h4 class="mt-1 mb-0" id="card_total_qty">—</h4>
                <p class="text-muted mb-0 small">Total Stock Quantity</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body py-3">
                <i class="ti ti-currency-dollar f-30 text-warning"></i>
                <h4 class="mt-1 mb-0" id="card_total_value">—</h4>
                <p class="text-muted mb-0 small">Total Stock Value</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="ti ti-report me-2"></i>Item-wise Stock Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm data-table" id="stockReportTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item No</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Unit Price (LKR)</th>
                                <th>Total Qty</th>
                                <th>Total Value (LKR)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td colspan="5" class="text-end">Totals:</td>
                                <td id="foot_total_qty">0</td>
                                <td id="foot_total_value">0.00</td>
                                <td colspan="2"></td>
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
    var totalQty   = 0;
    var totalValue = 0;
    var totalItems = 0;

    var table = $('#stockReportTable').DataTable({
        dom: '<"top"lBf>rt<"bottom"ip><"clear">',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.stock_report.index') }}",
            dataSrc: function (json) {
                // Compute totals from data
                totalQty   = 0;
                totalValue = 0;
                totalItems = json.data.length;

                json.data.forEach(function (row) {
                    totalQty   += parseFloat(row.total_quantity) || 0;
                    totalValue += parseFloat((row.total_value + '').replace(/,/g, '')) || 0;
                });

                $('#foot_total_qty').text(totalQty.toFixed(2));
                $('#foot_total_value').text(totalValue.toLocaleString('en-US', { minimumFractionDigits: 2 }));
                $('#card_total_items').text(json.recordsTotal ?? totalItems);
                $('#card_total_qty').text(totalQty.toFixed(2));
                $('#card_total_value').text('LKR ' + totalValue.toLocaleString('en-US', { minimumFractionDigits: 2 }));

                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_no',        name: 'item_no' },
            { data: 'item_code',      name: 'item_code' },
            { data: 'item_name',      name: 'item_name' },
            { data: 'unit_price',     name: 'unit_price',
              render: d => parseFloat(d || 0).toFixed(2) },
            { data: 'total_quantity', name: 'total_quantity',
              render: d => parseFloat(d || 0).toFixed(2) },
            { data: 'total_value',    name: 'total_value', orderable: false },
            { data: 'stock_status',   name: 'stock_status', orderable: false, searchable: false },
            { data: 'action',         name: 'action', orderable: false, searchable: false },
        ],
    });
});
</script>
@endsection
