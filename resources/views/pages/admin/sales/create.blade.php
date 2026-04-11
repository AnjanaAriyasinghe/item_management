@extends('layouts.main')

@section('title', 'New Sale / Receipt')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'New Sale')

@section('css')
<style>
    /* ── POS Billing Form Styles ── */
    .billing-page {
        background: #f4f6fc;
        min-height: calc(100vh - 140px);
    }
    .billing-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0,0,0,.07);
    }
    .billing-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px 16px 0 0;
        padding: 20px 28px;
    }
    .billing-card .card-header h4 {
        color: #fff;
        margin: 0;
        font-weight: 700;
        letter-spacing: .3px;
    }
    .section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #8b8fa8;
        margin-bottom: 10px;
    }
    /* Items table */
    #items-table thead th {
        background: #f0f3ff;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #555;
        border: none;
        padding: 10px 12px;
    }
    #items-table tbody td {
        vertical-align: middle;
        padding: 8px 10px;
    }
    .btn-add-row {
        background: linear-gradient(135deg,#43e97b,#38f9d7);
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 13px;
    }
    .btn-add-row:hover { opacity: .9; color:#fff; }
    .btn-remove-row {
        border-radius: 6px;
        padding: 4px 10px;
    }
    /* Summary box */
    .summary-box {
        background: linear-gradient(135deg,#1a1a2e 0%,#16213e 100%);
        border-radius: 14px;
        padding: 24px 28px;
        color: #fff;
    }
    .summary-box .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid rgba(255,255,255,.08);
        font-size: 15px;
    }
    .summary-box .summary-row:last-child { border-bottom: none; }
    .summary-box .summary-row.total-row {
        margin-top: 10px;
        font-size: 22px;
        font-weight: 800;
        color: #43e97b;
    }
    .summary-box .lbl { color: rgba(255,255,255,.72); }
    .summary-box .val { font-weight: 600; }
    .stock-badge {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 20px;
    }
    /* Action btns */
    .btn-save-print {
        background: linear-gradient(135deg,#667eea,#764ba2);
        border: none;
        color:#fff;
        font-weight: 700;
        border-radius: 10px;
        padding: 11px 28px;
        font-size: 15px;
        letter-spacing: .3px;
    }
    .btn-save-print:hover { opacity:.9; color:#fff; }
    .btn-save-only {
        background: #f0f3ff;
        border: 2px solid #667eea;
        color: #667eea;
        font-weight: 700;
        border-radius: 10px;
        padding: 11px 28px;
        font-size: 15px;
    }
    .btn-save-only:hover { background:#667eea; color:#fff; }
    /* Select2 overrides to match Bootstrap */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: #212529;
        padding-left: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-dropdown { z-index: 9999; }
</style>
@endsection

@section('content')
<div class="billing-page">
    <div class="row">
        <div class="col-xl-8 col-lg-8">
            <div class="card billing-card">
                <div class="card-header">
                    <h4><i class="ph-duotone ph-receipt me-2"></i>New Sale / Receipt</h4>
                </div>
                <div class="card-body p-4">

                    {{-- ── Customer & Date ── --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <p class="section-label mb-1">Customer</p>
                            <select id="customer_id" name="customer_id" class="form-select" style="border-radius:8px; width:100%">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->customer_code }}){{ $c->phone ? ' — '.$c->phone : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <p class="section-label mb-1">Sale Date</p>
                            <input type="date" id="sale_date" class="form-control" style="border-radius:8px"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    {{-- ── Items Table ── --}}
                    <p class="section-label">Items</p>
                    <div class="table-responsive mb-2">
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th style="width:36%">Item</th>
                                    <th style="width:12%">Qty</th>
                                    <th style="width:16%">Unit Price (Rs.)</th>
                                    <th style="width:15%">Line Total</th>
                                    <th style="width:13%">Stock</th>
                                    <th style="width:8%"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                {{-- rows injected by JS --}}
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-add-row mb-4" id="btn-add-row">
                        <i class="ph-duotone ph-plus-circle me-1"></i> Add Item
                    </button>

                    {{-- ── Note ── --}}
                    <p class="section-label mb-1">Note / Remark</p>
                    <textarea id="note" class="form-control mb-1" rows="2"
                              style="border-radius:8px" placeholder="Optional note for this receipt..."></textarea>

                </div>
            </div>
        </div>

        {{-- ── Right: Summary + Actions ── --}}
        <div class="col-xl-4 col-lg-4">

            {{-- Summary Box --}}
            <div class="summary-box mb-4">
                <div class="summary-row">
                    <span class="lbl">Subtotal</span>
                    <span class="val" id="disp-subtotal">Rs. 0.00</span>
                </div>
                <div class="summary-row">
                    <span class="lbl">Discount</span>
                    <span class="val text-warning" id="disp-discount">− Rs. 0.00</span>
                </div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span id="disp-total">Rs. 0.00</span>
                </div>
            </div>

            {{-- Discount Controls --}}
            <div class="card billing-card mb-3">
                <div class="card-body p-3">
                    <p class="section-label mb-2">Discount</p>
                    <div class="btn-group w-100 mb-2" role="group">
                        <input type="radio" class="btn-check" name="discount_type" id="dt-percent" value="percent" autocomplete="off">
                        <label class="btn btn-outline-primary btn-sm" for="dt-percent">% Percentage</label>
                        <input type="radio" class="btn-check" name="discount_type" id="dt-fixed" value="fixed" autocomplete="off" checked>
                        <label class="btn btn-outline-primary btn-sm" for="dt-fixed">Rs. Fixed</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text" id="discount-prefix">Rs.</span>
                        <input type="number" id="discount_value" class="form-control"
                               min="0" step="0.01" value="0" placeholder="0.00">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-grid gap-2">
                <button type="button" id="btn-save-print" class="btn btn-save-print">
                    <i class="ph-duotone ph-printer me-2"></i>Save &amp; Print Receipt
                </button>
                <button type="button" id="btn-save-only" class="btn btn-save-only">
                    <i class="ph-duotone ph-floppy-disk me-2"></i>Save Only
                </button>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary">
                    <i class="ph-duotone ph-arrow-left me-1"></i>Back to Sales List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Select2 CDN --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    //  ──────────────────────────────────────────
    //  Row template
    //  ──────────────────────────────────────────
    var rowIndex = 0;

    function newRow() {
        var i = rowIndex++;
        return '<tr id="row-' + i + '">' +
            '<td>' +
                '<select class="item-select" id="item-select-' + i + '" style="width:100%">' +
                    '<option value="">Search item…</option>' +
                '</select>' +
                '<small class="text-muted item-desc-' + i + '"></small>' +
            '</td>' +
            '<td>' +
                '<input type="number" class="form-control qty-input" id="qty-' + i + '" ' +
                       'min="0.01" step="0.01" value="1" style="border-radius:8px; min-width:70px">' +
            '</td>' +
            '<td>' +
                '<input type="number" class="form-control price-input" id="price-' + i + '" ' +
                       'min="0" step="0.01" value="0" style="border-radius:8px; min-width:90px">' +
            '</td>' +
            '<td>' +
                '<span class="fw-bold line-total" id="lt-' + i + '">0.00</span>' +
            '</td>' +
            '<td>' +
                '<span class="badge bg-info stock-badge" id="stock-' + i + '">—</span>' +
            '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-remove-row btn-sm" data-row="' + i + '">' +
                    '<i class="ti ti-x"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
    }

    //  ──────────────────────────────────────────
    //  Initialise Select2 on a row's item select
    //  ──────────────────────────────────────────
    function initSelect2(rowId) {
        var $sel = $('#item-select-' + rowId);
        $sel.select2({
            placeholder: 'Search item…',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: "{{ route('admin.sales.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (p) { return { term: p.term || '' }; },
                processResults: function (d) { return { results: d.results }; },
                cache: true,
            },
        });

        $sel.on('select2:select', function (e) {
            var d = e.params.data;
            $('#price-' + rowId).val(parseFloat(d.unit_price || 0).toFixed(2));
            $('.item-desc-' + rowId).text(d.item_description || '');
            var avail = parseFloat(d.available_stock) || 0;
            $('#stock-' + rowId)
                .text('Avail: ' + avail)
                .removeClass('bg-info bg-success bg-danger bg-warning')
                .addClass(avail > 10 ? 'bg-success' : avail > 0 ? 'bg-warning' : 'bg-danger');
            recalcRow(rowId);
            updateSummary();
        });

        $sel.on('select2:clear', function () {
            $('#price-' + rowId).val(0);
            $('.item-desc-' + rowId).text('');
            $('#stock-' + rowId).text('—').removeClass('bg-success bg-warning bg-danger').addClass('bg-info');
            recalcRow(rowId);
            updateSummary();
        });
    }

    //  ──────────────────────────────────────────
    //  Recalculate a single row's line total
    //  ──────────────────────────────────────────
    function recalcRow(rowId) {
        var qty   = parseFloat($('#qty-'   + rowId).val()) || 0;
        var price = parseFloat($('#price-' + rowId).val()) || 0;
        var lt    = qty * price;
        $('#lt-' + rowId).text(lt.toFixed(2));
        return lt;
    }

    //  ──────────────────────────────────────────
    //  Re-sum all rows → update summary panel
    //  ──────────────────────────────────────────
    function updateSummary() {
        var subtotal = 0;
        $('#items-body tr').each(function () {
            var rowId = $(this).attr('id').replace('row-', '');
            subtotal += recalcRow(rowId);
        });

        var dtype  = $('input[name="discount_type"]:checked').val();
        var dval   = parseFloat($('#discount_value').val()) || 0;
        var disAmt = 0;
        if (dtype === 'percent') {
            disAmt = subtotal * (dval / 100);
        } else {
            disAmt = Math.min(dval, subtotal);
        }
        var total = subtotal - disAmt;

        $('#disp-subtotal').text('Rs. ' + subtotal.toFixed(2));
        $('#disp-discount').text('− Rs. ' + disAmt.toFixed(2));
        $('#disp-total').text('Rs. ' + total.toFixed(2));
    }

    //  ──────────────────────────────────────────
    //  Events
    //  ──────────────────────────────────────────
    $(document).ready(function () {

        // Add first row on page load
        addRow();

        // Add row button
        $('#btn-add-row').on('click', addRow);

        // Remove row
        $(document).on('click', '.btn-remove-row', function () {
            var rowId = $(this).data('row');
            // Destroy select2 before removing DOM
            if ($('#item-select-' + rowId).hasClass('select2-hidden-accessible')) {
                $('#item-select-' + rowId).select2('destroy');
            }
            $('#row-' + rowId).remove();
            updateSummary();
        });

        // Input changes trigger recalc
        $(document).on('input', '.qty-input, .price-input', function () {
            updateSummary();
        });

        // Discount type toggle → update prefix label & recalc
        $('input[name="discount_type"]').on('change', function () {
            var isPercent = $(this).val() === 'percent';
            $('#discount-prefix').text(isPercent ? '%' : 'Rs.');
            updateSummary();
        });

        // Discount value change
        $('#discount_value').on('input', updateSummary);

        // Save & Print
        $('#btn-save-print').on('click', function () {
            submitForm(true);
        });

        // Save Only
        $('#btn-save-only').on('click', function () {
            submitForm(false);
        });
    });

    function addRow() {
        var html = newRow();
        $('#items-body').append(html);
        var id = rowIndex - 1;
        initSelect2(id);
    }

    //  ──────────────────────────────────────────
    //  Build payload and submit via AJAX
    //  ──────────────────────────────────────────
    function submitForm(openPrint) {
        // Collect items
        var items = [];
        var valid = true;
        $('#items-body tr').each(function () {
            var rowId    = $(this).attr('id').replace('row-', '');
            var itemId   = $('#item-select-' + rowId).val();
            var qty      = parseFloat($('#qty-'   + rowId).val()) || 0;
            var price    = parseFloat($('#price-' + rowId).val()) || 0;

            if (!itemId) {
                Swal.fire({ icon: 'warning', title: 'Missing Item', text: 'Please select an item in all rows.' });
                valid = false;
                return false; // break $.each
            }
            if (qty <= 0) {
                Swal.fire({ icon: 'warning', title: 'Invalid Qty', text: 'Quantity must be greater than 0.' });
                valid = false;
                return false;
            }
            items.push({ item_id: itemId, quantity: qty, unit_price: price });
        });

        if (!valid) return;
        if (items.length === 0) {
            Swal.fire({ icon: 'warning', title: 'No Items', text: 'Please add at least one item.' });
            return;
        }

        var saleDate = $('#sale_date').val();
        if (!saleDate) {
            Swal.fire({ icon: 'warning', title: 'Missing Date', text: 'Please select a sale date.' });
            return;
        }

        var payload = {
            _token:         "{{ csrf_token() }}",
            customer_id:    $('#customer_id').val() || null,
            sale_date:      saleDate,
            discount_type:  $('input[name="discount_type"]:checked').val(),
            discount_value: parseFloat($('#discount_value').val()) || 0,
            note:           $('#note').val(),
            items:          items,
        };

        // Disable buttons while saving
        $('#btn-save-print, #btn-save-only').prop('disabled', true).text('Saving…');

        $.ajax({
            url:         "{{ route('admin.sales.store') }}",
            method:      'POST',
            data:        JSON.stringify(payload),
            contentType: 'application/json',
            success: function (res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: res.message || 'Sale saved successfully.',
                        timer: 1800,
                        showConfirmButton: false,
                    }).then(function() {
                        if (openPrint && res.print_url) {
                            window.open(res.print_url, '_blank');
                        }
                        window.location.href = "{{ route('admin.sales.index') }}";
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'An error occurred.' });
                    resetButtons();
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors;
                var msg = errors
                    ? Object.values(errors).flat().join('\n')
                    : (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred.');
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
                resetButtons();
            },
        });
    }

    function resetButtons() {
        $('#btn-save-print').prop('disabled', false).html('<i class="ph-duotone ph-printer me-2"></i>Save &amp; Print Receipt');
        $('#btn-save-only').prop('disabled', false).html('<i class="ph-duotone ph-floppy-disk me-2"></i>Save Only');
    }
</script>
@endsection
