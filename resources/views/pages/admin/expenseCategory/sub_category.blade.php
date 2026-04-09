@extends('layouts.main')

@section('title', 'Expense Sub Categories')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Expense Sub Categories')
@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel"> <span id="model-main-title"></span> Sub Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitForm" method="POST" action="{{route('admin.expense_sub_categories.store')}}">
                    @csrf
                    <div class="form-group row g-4">
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="category_id" id="category_id" class="form-control category_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name">Category<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="name" name="name" placeholder="">
                                <label for="name">Sub Category<span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-close-new" data-bs-dismiss="modal">Close</button>
                <button type="button" id="submitFormBtn" class="btn btn-primary save-button">Save</button>
                <div class="spinner-border text-primary" role="status" style="display: none"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Sub Categories</h4>
            </div>
            <div class="card-body">
                @can('admin-common-expense_sub_categories-create')
                <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal" data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i> Create Sub Category</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-4 ">
                                <div class="form-group form-floating mb-0">
                                    <select name="search_category_id" id="search_category_id" class="form-control search_category_id form-control-custom-select">
                                        <option value=""></option>
                                    </select>
                                    <label for="name">Category<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-info" id="search">Search</button>
                            </div>
                        </div>
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Created By</th>
                                <th>Created At</th>
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
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: '/admin/getcategory'
            , method: 'GET'
            , success: function(response) {
                $('#category_id').empty();
                $("#category_id ").append($("<option />"));
                $.each(response.categories, function(key, option) {
                    $("#category_id").append($("<option />")
                        .val(option.id)
                        .text(option.name));
                });
                $('#search_category_id').empty();
                $("#search_category_id").append($("<option />").val("").text(""));
                $("#search_category_id").append($("<option />").val("all").text("All"));
                $.each(response.categories, function(key, option) {
                    $("#search_category_id").append($("<option />")
                        .val(option.id)
                        .text(option.name));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

    $(function() {
        var table = $('.data-table').DataTable({
            processing: true
            , serverSide: true
            , ajax: {
                url: "{{ route('admin.expense_sub_categories.index') }}"
                , data: function(d) {
                    d.category_id = $('#search_category_id').val();
                }
            }
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }
                , {
                    data: 'category.name'
                    , name: 'category.name'
                }
                , {
                    data: 'name'
                    , name: 'name'
                }
                , {
                    data: 'created_user.name'
                    , name: 'created_user.name'
                }
                , {
                    data: 'created_at'
                    , name: 'created_at'
                    , render: function(data) {
                        // Convert the data to a Date object and format it
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US'); // You can customize the locale
                    }
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }
            ]

        });
        $("#search").on("click", function() {
            table.draw();
        });
    });
    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
        $('#submitForm').attr('action', "{{ route('admin.expense_sub_categories.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/admin/expense_sub_categories/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#category_id').val(response.su_category.category_id).trigger('change');
                $('#name').val(response.su_category.name);
                $('#createModel').modal('show');
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

</script>
@endsection
