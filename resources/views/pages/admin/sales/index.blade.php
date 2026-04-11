@extends('layouts.main')

@section('title', 'Sales & Receipts')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Sales & Receipts')

@section('content')

{{-- ══════════════════════════════════════
     View Modal
══════════════════════════════════════ --}}
<div class="modal fade" id="saleViewModal" tabindex="-1" role="dialog" aria-labelledby="saleViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border:none; border-radius:16px; overflow:hidden;">

            {{-- Modal Header --}}
            <div class="modal-header" style="background:linear-gradient(135deg,#667eea,#764ba2); padding:18px 28px;">
                <h5 class="modal-title text-white fw-bold" id="saleViewModalLabel">
                    <i class="ph-duotone ph-receipt me-2"></i>
                    Sale Details — <span id="vm-sale-no"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div id="vm-loader" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">Loading...</p>
                </div>

                <div id="vm-content" style="display:none;">

                    {{-- Info Bar --}}
                    <div class="row g-0" style="background:#f8f9fc; border-bottom:2px solid #eef0f8;">
                        <div class="col-md-3 p-3 border-end">
                            <p class="mb-0" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#999;">Sale No.</p>
                            <p class="mb-0 fw-bold text-primary" id="vm-info-sale-no" style="font-size:16px;"></p>
                        </div>
                        <div class="col-md-3 p-3 border-end">
                            <p class="mb-0" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#999;">Date</p>
                            <p class="mb-0 fw-bold" id="vm-info-date" style="font-size:15px;"></p>
                        </div>
                        <div class="col-md-3 p-3 border-end">
                            <p class="mb-0" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#999;">Customer</p>
                            <p class="mb-0 fw-bold" id="vm-info-customer" style="font-size:15px;"></p>
                            <small class="text-muted" id="vm-info-phone"></small>
                        </div>
                        <div class="col-md-3 p-3">
                            <p class="mb-0" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#999;">Served By</p>
                            <p class="mb-0 fw-bold" id="vm-info-created-by" style="font-size:15px;"></p>
                        </div>
                    </div>

                    {{-- Item Table --}}
                    <div class="p-4">
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#8b8fa8;" class="mb-2">
                            Items Sold
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="vm-items-table">
                                <thead style="background:linear-gradient(135deg,#667eea,#764ba2);">
                                    <tr>
                                        <th style="color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">#</th>
                                        <th style="color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">Item Code</th>
                                        <th style="color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;">Item Name</th>
                                        <th style="color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;text-align:right;">Qty</th>
                                        <th style="color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;text-align:right;">Unit Price</th>
                                        <th style="color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;text-align:right;">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody id="vm-items-body"></tbody>
                            </table>
                        </div>

                        {{-- Totals + Note --}}
                        <div class="row mt-4">
                            {{-- Note --}}
                            <div class="col-md-6">
                                <div id="vm-note-wrap" style="display:none;">
                                    <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#8b8fa8;" class="mb-1">Note</p>
                                    <div style="background:#f8f9fc;border-left:4px solid #667eea;padding:10px 14px;border-radius:0 8px 8px 0;font-size:14px;color:#555;" id="vm-note"></div>
                                </div>
                            </div>

                            {{-- Totals --}}
                            <div class="col-md-6 ms-auto">
                                <div style="background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:14px;padding:20px 24px;color:#fff;">
                                    <div class="d-flex justify-content-between mb-2" style="font-size:14px;">
                                        <span style="color:rgba(255,255,255,.72);">Subtotal</span>
                                        <span class="fw-bold" id="vm-subtotal"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2" style="font-size:14px;" id="vm-discount-row">
                                        <span style="color:rgba(255,255,255,.72);" id="vm-discount-label">Discount</span>
                                        <span class="fw-bold text-warning" id="vm-discount"></span>
                                    </div>
                                    <hr style="border-color:rgba(255,255,255,.15);margin:10px 0;">
                                    <div class="d-flex justify-content-between" style="font-size:22px;font-weight:800;color:#43e97b;">
                                        <span>Total</span>
                                        <span id="vm-total"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /vm-content -->
            </div><!-- /modal-body -->

            <div class="modal-footer" style="border-top:1px solid #eef0f8; padding:14px 24px;">
                <a href="#" target="_blank" id="vm-print-btn" class="btn btn-success">
                    <i class="ti ti-printer me-1"></i> Print Receipt
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     Main Table Card
══════════════════════════════════════ --}}
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
        // ── DataTable ──────────────────────────────────────────
        $('#sales-datatable').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sales.index') }}",
            order: [[0, 'desc']],
            columns: [
                { data: 'id',              name: 'id' },
                { data: 'sale_no',         name: 'sale_no' },
                { data: 'customer_name',   name: 'customer_name',  orderable: false },
                { data: 'sale_date_fmt',   name: 'sale_date',      orderable: true },
                { data: 'total_amount_fmt',name: 'total_amount' },
                {
                    data: 'created_by', name: 'created_by',
                    render: function(data, type, row) {
                        return row.createdBy ? row.createdBy.name : '—';
                    },
                    orderable: false, searchable: false
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        // ── View button ────────────────────────────────────────
        $(document).on('click', '.btnSaleView', function () {
            var id = $(this).data('id');

            // Reset modal
            $('#vm-content').hide();
            $('#vm-loader').show();
            $('#vm-items-body').empty();
            $('#saleViewModal').modal('show');

            $.ajax({
                url: '/admin/sales/' + id + '/detail',
                method: 'GET',
                success: function (res) {
                    if (!res.status) {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Could not load sale.' });
                        $('#saleViewModal').modal('hide');
                        return;
                    }
                    var s = res.sale;

                    // Header info
                    $('#vm-sale-no').text(s.sale_no);
                    $('#vm-info-sale-no').text(s.sale_no);
                    $('#vm-info-date').text(s.sale_date);
                    $('#vm-info-customer').text(s.customer_name);
                    $('#vm-info-phone').text(s.customer_phone !== '—' ? '📞 ' + s.customer_phone : '');
                    $('#vm-info-created-by').text(s.created_by);

                    // Items
                    $.each(s.items, function (i, item) {
                        $('#vm-items-body').append(
                            '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td><span class="badge bg-light text-dark border">' + item.item_code + '</span></td>' +
                                '<td class="fw-semibold">' + item.item_name + '</td>' +
                                '<td class="text-end">' + item.quantity + '</td>' +
                                '<td class="text-end">Rs. ' + item.unit_price + '</td>' +
                                '<td class="text-end fw-bold text-primary">Rs. ' + item.line_total + '</td>' +
                            '</tr>'
                        );
                    });

                    // Totals
                    $('#vm-subtotal').text('Rs. ' + s.subtotal);
                    $('#vm-total').text('Rs. ' + s.total_amount);

                    if (parseFloat(s.discount_amount.replace(/,/g, '')) > 0) {
                        var dlabel = s.discount_type === 'percent'
                            ? 'Discount (' + s.discount_value + '%)'
                            : 'Discount (Fixed)';
                        $('#vm-discount-label').text(dlabel);
                        $('#vm-discount').text('− Rs. ' + s.discount_amount);
                        $('#vm-discount-row').show();
                    } else {
                        $('#vm-discount-row').hide();
                    }

                    // Note
                    if (s.note) {
                        $('#vm-note').text(s.note);
                        $('#vm-note-wrap').show();
                    } else {
                        $('#vm-note-wrap').hide();
                    }

                    // Print link
                    $('#vm-print-btn').attr('href', '/admin/sales/' + id);

                    $('#vm-loader').hide();
                    $('#vm-content').fadeIn(200);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message : 'Could not load sale details.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    $('#saleViewModal').modal('hide');
                }
            });
        });
    });
</script>
@endsection
