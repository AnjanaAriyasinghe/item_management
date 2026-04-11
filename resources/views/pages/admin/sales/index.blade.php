@extends('layouts.main')

@section('title', 'Sales & Receipts')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Sales & Receipts')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0"><i class="ph-duotone ph-receipt me-2"></i>Sales &amp; Receipts</h4>
                <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm">
                    <i class="ph-duotone ph-plus-circle me-1"></i> New Sale
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm data-table" id="sales-datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sale No</th>
                                <th>Customer</th>
                                <th>Sale Date</th>
                                <th>Total</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#sales-datatable').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sales.index') }}",
            order: [[0, 'desc']],
            columns: [
                { data: 'id',             name: 'id' },
                { data: 'sale_no',        name: 'sale_no' },
                { data: 'customer_name',  name: 'customer_name', orderable: false },
                { data: 'sale_date_fmt',  name: 'sale_date', orderable: true },
                { data: 'total_amount_fmt', name: 'total_amount' },
                {
                    data: 'created_by', name: 'created_by',
                    render: function(data, type, row) {
                        return row.created_by ? (row.createdBy ? row.createdBy.name : '-') : '-';
                    },
                    orderable: false, searchable: false
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endsection
