@extends('layouts.main')

@section('title', 'Stock Management')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Add Items to Stock')

@section('content')
<!-- [ Main Content ] start -->

{{-- ══════════════════════════════════════
     Create / Edit Modal
══════════════════════════════════════ --}}
<div class="modal fade" id="stockModal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">
                    <i class="ti ti-stack-2 me-1"></i>
                    <span id="modal-mode-title">Add</span> Stock Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stockForm" method="POST" action="{{ route('admin.stocks.store') }}">
                    @csrf
                    <div class="row g-3">

                        {{-- Item Select (Select2) --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Select Item <span class="text-danger">*</span></label>
                            <select id="item_select" name="item_id" class="form-control" style="width:100%;" required>
                                <option value="">— Search by item name or code —</option>
                            </select>
                        </div>

                        {{-- Auto-fill read-only display fields --}}
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control bg-light" type="text" id="disp_item_no" placeholder=" " readonly>
                                <label>Item No</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control bg-light" type="text" id="disp_item_code" placeholder=" " readonly>
                                <label>Item Code</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control bg-light" type="text" id="disp_item_name" placeholder=" " readonly>
                                <label>Item Name</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea class="form-control bg-light" id="disp_description" placeholder=" "
                                          style="height:70px;" readonly></textarea>
                                <label>Item Description</label>
                            </div>
                        </div>

                        <hr class="my-1">

                        {{-- Editable stock fields --}}
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="transaction_type" name="transaction_type" required>
                                    <option value="">— Select —</option>
                                    <option value="in">Stock In</option>
                                    <option value="out">Stock Out</option>
                                </select>
                                <label for="transaction_type">Transaction Type <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control" type="number" id="unit_price" name="unit_price"
                                       placeholder=" " step="0.01" min="0" required>
                                <label for="unit_price">Unit Price <span class="text-danger">*</span></label>
                            </div>
                            <small class="text-muted">
                                <i class="ti ti-info-circle"></i>
                                Editing this will also update the item's unit price.
                            </small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input class="form-control" type="number" id="stock_quantity" name="stock_quantity"
                                       placeholder=" " step="0.01" min="0" required>
                                <label for="stock_quantity">Stock Quantity <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input class="form-control" type="text" id="remark" name="remark" placeholder=" ">
                                <label for="remark">Remark</label>
                            </div>
                        </div>

                    </div><!-- /.row -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <div class="spinner-border text-primary" role="status" style="display:none;" id="stockSpinner"></div>
                <button type="button" id="stockSaveBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     View Modal
══════════════════════════════════════ --}}
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-stack-2 me-1"></i> Stock Entry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr><th style="width:30%">Item No</th>    <td id="v_item_no">—</td></tr>
                        <tr><th>Item Code</th>                    <td id="v_item_code">—</td></tr>
                        <tr><th>Item Name</th>                    <td id="v_item_name">—</td></tr>
                        <tr><th>Transaction Type</th>             <td id="v_transaction_type">—</td></tr>
                        <tr><th>Unit Price</th>                   <td id="v_unit_price">—</td></tr>
                        <tr><th>Stock Quantity</th>               <td id="v_stock_quantity">—</td></tr>
                        <tr><th>Remark</th>                       <td id="v_remark">—</td></tr>
                        <tr><th>Created By</th>                   <td id="v_created_by">—</td></tr>
                        <tr><th>Created At</th>                   <td id="v_created_at">—</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     Import Modal
══════════════════════════════════════ --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="ti ti-file-spreadsheet me-1"></i> Bulk Import Stock via Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                {{-- Column format guide --}}
                <div class="alert alert-info py-2 mb-3">
                    <strong><i class="ti ti-info-circle me-1"></i>Expected Excel Columns (Row 1 = Headings):</strong><br>
                    <code>Type</code> | <code>Item Code</code> | <code>Item Name</code> | <code>Stock Qty</code> | <code>Unit Price</code> | <code>Remark</code> | <code>Date</code>
                    <ul class="mb-0 mt-1 small">
                        <li><strong>Type</strong>: <code>in</code> or <code>out</code></li>
                        <li><strong>Item Code</strong>: must match an existing item (PK lookup)</li>
                        <li><strong>Unit Price</strong>: if changed, the item's unit price will be updated</li>
                        <li><strong>Date</strong>: format <code>YYYY-MM-DD</code></li>
                    </ul>
                </div>

                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Excel File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file"
                               accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Accepted: .xlsx, .xls, .csv &mdash; max 5 MB</small>
                    </div>
                </form>

                {{-- Result area (hidden until after upload) --}}
                <div id="importResultArea" style="display:none;">
                    <hr>
                    <div id="importSuccessMsg" class="alert alert-success py-2" style="display:none;"></div>
                    <div id="importErrorTable" style="display:none;">
                        <p class="text-danger fw-semibold mb-1"><i class="ti ti-alert-circle me-1"></i>Row Errors:</p>
                        <div class="table-responsive" style="max-height:220px;overflow-y:auto;">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-danger"><tr><th>Row #</th><th>Error</th></tr></thead>
                                <tbody id="importErrorBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <div class="spinner-border text-success" role="status" style="display:none;" id="importSpinner"></div>
                <button type="button" id="importSaveBtn" class="btn btn-success">
                    <i class="ti ti-upload me-1"></i> Import
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     Table Card
══════════════════════════════════════ --}}
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="ti ti-stack-2 me-2"></i>Stock Entries</h5>
                @can('admin-common-stocks-create')
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.stocks.template') }}" class="btn btn-outline-success btn-sm">
                        <i class="ti ti-download me-1"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-success btn-sm" id="btnImportStock">
                        <i class="ti ti-file-spreadsheet me-1"></i> Upload Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnAddStock">
                        <i class="ph-duotone ph-plus-circle me-1"></i> Add Stock Entry
                    </button>
                </div>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm data-table" id="stockTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Item No</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Stock Qty</th>
                                <th>Unit Price</th>
                                <th>Remark</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Stock Date</th>
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
<!-- [ Main Content ] end -->
@endsection

@section('scripts')
{{-- Select2 CSS & JS (CDN) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container .select2-selection--single {
        height: calc(3.5rem + 2px) !important;
        padding: 1rem 0.75rem 0 !important;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5 !important;
        color: #212529;
        padding-left: 0;
        padding-top: 0.4rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(3.5rem + 2px) !important;
    }
    .select2-dropdown { z-index: 9999; }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {

    // ─── DataTable ────────────────────────────────────────────
    var table = $('#stockTable').DataTable({
        dom: '<"top"lBf>rt<"bottom"ip><"clear">',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.stocks.index') }}",
        columns: [
            { data: 'DT_RowIndex',         name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'transaction_type_badge', name: 'transaction_type', orderable: false, searchable: false },
            { data: 'item_no',             name: 'item.item_no',    orderable: false },
            { data: 'item_code',           name: 'item.item_code',  orderable: false },
            { data: 'item_name',           name: 'item.item_name',  orderable: false },
            { data: 'stock_quantity',      name: 'stock_quantity' },
            { data: 'unit_price',          name: 'unit_price',
              render: d => d !== null ? parseFloat(d).toFixed(2) : '—' },
            { data: 'remark',              name: 'remark',          orderable: false },
            { data: 'created_user.name',   name: 'created_user.name' },
            { data: 'created_at_fmt',      name: 'created_at',      orderable: false },
            { data: 'stock_date',           name: 'stock_date', orderable: false,
              render: d => d ? d : '—' },
            { data: 'action',              name: 'action', orderable: false, searchable: false },
        ],
    });

    // ─── Init Select2 ────────────────────────────────────────
    function initSelect2() {
        $('#item_select').select2({
            dropdownParent: $('#stockModal'),
            placeholder: '— Search by item name or code —',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: "{{ route('admin.stocks.search') }}",
                dataType: 'json',
                delay: 300,
                data: params => ({ term: params.term || '' }),
                processResults: data => ({ results: data.results }),
                cache: true,
            },
        }).on('select2:select', function (e) {
            var d = e.params.data;
            $('#disp_item_no').val(d.item_no);
            $('#disp_item_code').val(d.item_code);
            $('#disp_item_name').val(d.item_name);
            $('#disp_description').val(d.item_description || '');
            $('#unit_price').val(parseFloat(d.unit_price || 0).toFixed(2));
        }).on('select2:clear', function () {
            $('#disp_item_no, #disp_item_code, #disp_item_name, #disp_description, #unit_price').val('');
        });
    }

    // ─── Open Add modal ──────────────────────────────────────
    $('#btnAddStock').on('click', function () {
        $('#modal-mode-title').text('Add');
        $('#stockForm')[0].reset();
        $('#stockForm').attr('action', "{{ route('admin.stocks.store') }}");
        $('input[name="_method"]').remove();
        // Reset select2 & display fields
        if ($('#item_select').hasClass('select2-hidden-accessible')) {
            $('#item_select').select2('destroy');
        }
        $('#item_select').val(null);
        $('#disp_item_no, #disp_item_code, #disp_item_name, #disp_description').val('');
        $('#transaction_type').val('');
        initSelect2();
        $('#stockModal').modal('show');
    });

    // ─── Edit button ─────────────────────────────────────────
    $(document).on('click', '.btnStockEdit', function () {
        var id = $(this).data('id');
        $('#modal-mode-title').text('Edit');
        $('input[name="_method"]').remove();
        $('#stockForm').append('<input type="hidden" name="_method" value="PUT">');
        $('#stockForm').attr('action', "{{ url('admin/stocks') }}/" + id);

        $.ajax({
            url: "{{ url('admin/stocks') }}/" + id + "/edit",
            method: 'GET',
            success: function (r) {
                // Destroy existing Select2 and re-init
                if ($('#item_select').hasClass('select2-hidden-accessible')) {
                    $('#item_select').select2('destroy');
                }
                $('#item_select').empty();
                initSelect2();

                // Inject existing item as a Select2 option + select it
                var option = new Option(r.select_text, r.item.id, true, true);
                $('#item_select').append(option).trigger('change');

                // Fill display fields
                $('#disp_item_no').val(r.item.item_no);
                $('#disp_item_code').val(r.item.item_code);
                $('#disp_item_name').val(r.item.item_name);
                $('#disp_description').val(r.item.item_description || '');

                // Fill editable fields
                $('#transaction_type').val(r.stock.transaction_type || '');
                $('#unit_price').val(parseFloat(r.stock.unit_price).toFixed(2));
                $('#stock_quantity').val(r.stock.stock_quantity);
                $('#remark').val(r.stock.remark || '');

                $('#stockModal').modal('show');
            },
            error: function (xhr) { console.error(xhr.responseText); }
        });
    });

    // ─── View button ─────────────────────────────────────────
    $(document).on('click', '.btnStockView', function () {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ url('admin/stocks') }}/" + id + "/edit",
            method: 'GET',
            success: function (r) {
                $('#v_item_no').text(r.item.item_no || '—');
                $('#v_item_code').text(r.item.item_code || '—');
                $('#v_item_name').text(r.item.item_name || '—');
                var typeLabel = r.stock.transaction_type === 'out'
                    ? '<span class="badge bg-danger">Stock Out</span>'
                    : '<span class="badge bg-success">Stock In</span>';
                $('#v_transaction_type').html(typeLabel);
                $('#v_unit_price').text(parseFloat(r.stock.unit_price).toFixed(2));
                $('#v_stock_quantity').text(r.stock.stock_quantity);
                $('#v_remark').text(r.stock.remark || '—');
                $('#v_created_by').text(r.created_name || '—');
                $('#v_created_at').text(r.stock.created_at ? new Date(r.stock.created_at).toLocaleDateString('en-US') : '—');
                $('#viewModal').modal('show');
            },
            error: function (xhr) { console.error(xhr.responseText); }
        });
    });

    // ─── Save button ─────────────────────────────────────────
    $('#stockSaveBtn').on('click', function () {
        var formData = new FormData($('#stockForm')[0]);
        var actionUrl = $('#stockForm').attr('action');

        $('#stockSaveBtn').hide();
        $('#stockSpinner').show();

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    $('#stockModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message,
                                timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON?.errors;
                var msg = errors
                    ? Object.values(errors).flat().join('\n')
                    : (xhr.responseJSON?.message || 'Something went wrong.');
                Swal.fire({ icon: 'error', title: 'Validation Error', text: msg });
            },
            complete: function () {
                $('#stockSaveBtn').show();
                $('#stockSpinner').hide();
            }
        });
    });

    // ─── Import Excel ─────────────────────────────────────────
    $('#btnImportStock').on('click', function () {
        $('#importForm')[0].reset();
        $('#importResultArea').hide();
        $('#importSuccessMsg').hide().text('');
        $('#importErrorTable').hide();
        $('#importErrorBody').empty();
        $('#importModal').modal('show');
    });

    $('#importSaveBtn').on('click', function () {
        var fileInput = $('#excel_file')[0];
        if (!fileInput.files.length) {
            Swal.fire({ icon: 'warning', title: 'No File', text: 'Please select an Excel file to upload.' });
            return;
        }

        var formData = new FormData($('#importForm')[0]);
        $('#importSaveBtn').hide();
        $('#importSpinner').show();
        $('#importResultArea').hide();

        $.ajax({
            url: "{{ route('admin.stocks.import') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#importResultArea').show();

                if (res.imported_count > 0) {
                    $('#importSuccessMsg').show().text(
                        '✓ ' + res.imported_count + ' row(s) imported successfully.'
                    );
                    table.ajax.reload();
                }

                if (res.errors && res.errors.length > 0) {
                    $('#importErrorTable').show();
                    $.each(res.errors, function (i, e) {
                        $('#importErrorBody').append(
                            '<tr><td>' + e.row + '</td><td>' + e.error + '</td></tr>'
                        );
                    });
                }

                if (res.imported_count === 0 && (!res.errors || res.errors.length === 0)) {
                    Swal.fire({ icon: 'info', title: 'No Data', text: 'No rows were found or all rows were empty.' });
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON?.errors;
                var msg = errors
                    ? Object.values(errors).flat().join('\n')
                    : (xhr.responseJSON?.message || 'Something went wrong.');
                Swal.fire({ icon: 'error', title: 'Import Failed', text: msg });
            },
            complete: function () {
                $('#importSaveBtn').show();
                $('#importSpinner').hide();
            }
        });
    });

});

// ─── Delete handler ───────────────────────────────────────
function handleDelete(url, data) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This stock entry will be deleted permanently.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                data: { ...data, _method: 'DELETE' },
                success: function (response) {
                    if (response.status) {
                        $('#stockTable').DataTable().ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message,
                                    timer: 2000, showConfirmButton: false });
                    }
                },
                error: function (xhr) {
                    Swal.fire({ icon: 'error', title: 'Error',
                                text: xhr.responseJSON?.message || 'Could not delete.' });
                }
            });
        }
    });
}
</script>
@endsection
