@extends('layouts.main')

@section('title', 'Items')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Items')

@section('content')
    <!-- [ Main Content ] start -->

    {{-- ══════════════════════════════════════
    Create / Edit Modal
    ══════════════════════════════════════ --}}
    <div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModelLabel">
                        <span id="model-main-title"></span> Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitForm" method="POST" action="{{ route('admin.items.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="item_no" name="item_no" placeholder=" ">
                                    <label for="item_no">Item No <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="item_code" name="item_code" placeholder=" ">
                                    <label for="item_code">Item Code <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="item_name" name="item_name" placeholder=" ">
                                    <label for="item_name">Item Name <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="item_description" name="item_description"
                                        placeholder=" " style="height:100px;"></textarea>
                                    <label for="item_description">Item Description</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="unit_price" name="unit_price"
                                        placeholder=" ">
                                    <label for="unit_price">Unit Price <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Item Photo</label>
                                <input class="form-control" type="file" id="item_photo" name="item_photo" accept="image/*">
                                <div id="photo_preview_wrapper" class="mt-2" style="display:none;">
                                    <img id="photo_preview" src="" alt="Current Photo"
                                        style="max-height:120px;border-radius:8px;border:1px solid #dee2e6;">
                                    <p class="text-muted small mt-1 mb-0">Current photo — upload a new one to replace it.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <div class="spinner-border text-primary" role="status" style="display:none;" id="formSpinner"></div>
                    <button type="button" id="submitFormBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
    View Modal
    ══════════════════════════════════════ --}}
    <div class="modal fade" id="viewModel" tabindex="-1" role="dialog" aria-labelledby="viewModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModelLabel">
                        <i class="ti ti-package me-1"></i> Item Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        {{-- Photo --}}
                        <div class="col-md-4 text-center">
                            <div id="view_photo_wrapper">
                                <img id="view_photo" src="" alt="Item Photo"
                                    style="width:100%;max-width:200px;height:200px;object-fit:cover;border-radius:12px;border:1px solid #dee2e6;">
                            </div>
                            <!-- <div id="view_no_photo" class="d-flex align-items-center justify-content-center"
                                                                style="width:100%;max-width:200px;height:200px;border-radius:12px;background:#f5f5f5;border:1px dashed #ccc;margin:0 auto;">
                                                                <span class="text-muted"><i class="ti ti-photo-off f-40"></i><br>No Photo</span>
                                                            </div> -->
                        </div>
                        {{-- Details --}}
                        <div class="col-md-8">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width:40%">Item No</th>
                                        <td id="view_item_no">—</td>
                                    </tr>
                                    <tr>
                                        <th>Item Code</th>
                                        <td id="view_item_code">—</td>
                                    </tr>
                                    <tr>
                                        <th>Item Name</th>
                                        <td id="view_item_name">—</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td id="view_item_description">—</td>
                                    </tr>
                                    <tr>
                                        <th>Unit Price</th>
                                        <td id="view_unit_price">—</td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td id="view_created_by">—</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td id="view_created_at">—</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                <div class="card-header">
                    <h4>Users</h4>
                </div>

                <div class="card-body">
                    @can('admin-common-users-create')
                        <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal"
                            data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i>
                            Create Item</i></button>
                    @endcan
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-sm data-table" id="itemsTable">
                                <thead>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Item No</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Item Description</th>
                                    <th>Unit Price</th>
                                    <th>Created By</th>
                                    <th>Action</th>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
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

            // ─── DataTable ────────────────────────────────────
            var table = $('#itemsTable').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.items.index') }}",
                columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'item_photo',
                    name: 'item_photo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'item_no',
                    name: 'item_no'
                },
                {
                    data: 'item_code',
                    name: 'item_code'
                },
                {
                    data: 'item_name',
                    name: 'item_name'
                },
                {
                    data: 'item_description',
                    name: 'item_description',
                    orderable: false
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'created_user.name',
                    name: 'created_user.name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                ],
            });

            // ─── Reset modal on open (Create mode) ───────────
            $('#createModel').on('show.bs.modal', function (e) {
                if (!$(e.relatedTarget) || !$(e.relatedTarget).hasClass('btnEdit')) {
                    $('#model-main-title').text('Create');
                    $('#submitForm')[0].reset();
                    $('#submitForm').attr('action', "{{ route('admin.items.store') }}");
                    $('input[name="_method"]').remove();
                    $('#photo_preview_wrapper').hide();
                }
            });

            // ─── View button ──────────────────────────────────
            $(document).on('click', '.btnView', function () {
                var id = $(this).data('id');

                $.ajax({
                    url: '/admin/items/' + id + '/edit',
                    method: 'GET',
                    success: function (response) {
                        var item = response.item;

                        $('#view_item_no').text(item.item_no || '—');
                        $('#view_item_code').text(item.item_code || '—');
                        $('#view_item_name').text(item.item_name || '—');
                        $('#view_item_description').text(item.item_description || '—');
                        $('#view_unit_price').text(item.unit_price || '—');
                        $('#view_created_at').text(item.created_at ? new Date(item.created_at).toLocaleDateString('en-US') : '—');

                        // Photo
                        if (response.photo_url) {
                            $('#view_photo')
                                .attr('src', response.photo_url)
                                .attr('alt', 'No Photo');
                            $('#view_photo_wrapper').show();
                            $('#view_no_photo').hide();
                        } else {
                            $('#view_photo_wrapper').hide();
                            $('#view_no_photo').show();
                        }

                        $('#viewModel').modal('show');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // ─── Edit button ──────────────────────────────────
            $(document).on('click', '.btnEdit', function () {
                var id = $(this).data('id');
                $('#model-main-title').text('Edit');
                $('#submitForm').attr('action', "{{ route('admin.items.update', '') }}/" + id);
                $('input[name="_method"]').remove();
                $('#submitForm').append('<input type="hidden" name="_method" value="PUT">');

                $.ajax({
                    url: '/admin/items/' + id + '/edit',
                    method: 'GET',
                    success: function (response) {
                        $('#item_no').val(response.item.item_no);
                        $('#item_code').val(response.item.item_code);
                        $('#item_name').val(response.item.item_name);
                        $('#item_description').val(response.item.item_description);
                        if (response.photo_url) {
                            $('#photo_preview').attr('src', response.photo_url);
                            $('#photo_preview_wrapper').show();
                        } else {
                            $('#photo_preview_wrapper').hide();
                        }
                        $('#createModel').modal('show');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });



        });

        // ─── Delete handler ───────────────────────────────
        function handleDelete(url, data) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This item will be deleted permanently.",
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
                        data: {
                            ...data,
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            if (response.status) {
                                $('#itemsTable').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Could not delete.'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection