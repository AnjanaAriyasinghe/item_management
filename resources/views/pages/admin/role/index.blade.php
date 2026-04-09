@extends('layouts.main')

@section('title', 'Roles')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Roles')

@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="createModel" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="createModelLabel"><span id="model-main-title"></span> Use role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form id="submitForm" method="POST" action="{{route('admin.roles.store')}}">
                        @csrf
                        <!-- Role Name Input -->
                        <div class="form-group row g-4">
                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="text" id="roleName" name="name" placeholder="Role Name">
                                    <label for="roleName">Role Name <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Rounded Ribbon -->
                        <!-- Dynamic Permissions Sections -->
                        <div class="row mt-4">
                            @foreach($permissions as $main => $permission)
                            <div class="col-6">
                                <div class="card ribbon-box border shadow-none mb-2  mt-2">
                                    <div class="card-body text-muted">
                                        <!-- Main Permission Section -->
                                        <a href="#index_{{$main}}" data-bs-toggle="collapse" aria-expanded="false" role="button" class=" text-capitalize left">
                                            <button class="btn btn-primary text-capitalize">{{ str_replace('_', ' ', $main) }}</button>
                                        </a>
                                        <div id="index_{{$main}}" class="ribbon-content mt-2 text-muted collapse">
                                            @foreach($permission as $key => $permit)
                                            <div class="col-10">
                                                <div class="card ribbon-box border shadow-none mb-4 parent">
                                                    <div class="card-body">
                                                        <!-- Sub Permission Section -->
                                                        <a href="#parent_{{$key}}" data-bs-toggle="collapse" aria-expanded="false" role="button" class="btn btn-secondary  text-capitalize">
                                                            {{ str_replace('_', ' ', $key) }}
                                                        </a>
                                                        <h5 class="fs-14 text-end text-capitalize">
                                                            <input class="parent-checkbox" type="checkbox" data-group="parent_{{$key}}">
                                                            Check all in {{ str_replace('_', ' ', $key) }} section
                                                        </h5>
                                                        <div id="parent_{{$key}}" class="ribbon-content mt-4 text-muted collapse">
                                                            <div class="row">
                                                                @foreach($permit as $index => $parent)
                                                                <div class="col-xxl-10 col-xl-10 col-lg-10 text-capitalize mb-3 mt-2">
                                                                    <div class="card ribbon-box border shadow-none text-capitalize">
                                                                        <div class="card-body">
                                                                            <div class="ribbon ribbon-info">
                                                                                {{ str_replace('_', ' ', $index) }}
                                                                            </div>
                                                                            <div class="ribbon-content mt-4 text-muted child">
                                                                                <!-- Permissions -->
                                                                                @foreach($parent as $i => $val)
                                                                                <div>
                                                                                    <input class="child-checkbox" type="checkbox" data-group="parent_{{$key}}" name="permissions[]" id="permission_{{$val['id']}}" value="{{$val['id']}}">
                                                                                    {{ str_replace('_', ' ', $val['name']) }}
                                                                                </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Dynamic Permissions Sections end -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-close-new" data-bs-dismiss="modal">Close</button>
                <button type="button" id="submitFormBtn" class="btn btn-primary save-button">Save</button>
                <div class="spinner-border text-primary" role="status" style="display: none"></div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>User Role</h4>
            </div>
            <div class="card-body">
                @can('admin-common-user_roles-create')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModel"> <i class="ph-duotone ph-plus-circle"></i> Create User Role</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Name</th>
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
    $(function() {
        var table = $('.data-table').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">'
            , buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
            , processing: true
            , serverSide: true
            , ajax: "{{ route('admin.roles.index')}}"
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }
                , {
                    data: 'name'
                    , name: 'name'
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
        , });
    });

    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
            $('#submitForm').attr('action', "{{ route('admin.roles.update', '') }}/" + id);
            $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/admin/roles/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#roleName').val(response.role.name);
                $('input[type=checkbox]').prop('checked', false);
                $('.collapse').removeClass('show').attr('aria-expanded', 'false');
                response.role.permissions.forEach(function(permissionId) {
                    var permissionCheckbox = $('#permission_' + permissionId.id);
                    permissionCheckbox.prop('checked', true);

                    var parentCollapse = permissionCheckbox.closest('.collapse');
                    if (parentCollapse.length) {
                        var parentId = parentCollapse.attr('id');
                        $('a[href="index_' + parentId + '"]').attr('aria-expanded', 'true');
                        parentCollapse.addClass('show');
                    }
                    var mainParentCollapse = permissionCheckbox.closest('.ribbon-content');
                    if (mainParentCollapse.length) {
                        var mainId = mainParentCollapse.attr('id');
                        $('a[href="index_' + mainId + '"]').attr('aria-expanded', 'true');
                    }
                });
                $('#createModel').modal('show');
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
</script>

<script>
    // Handle Check All functionality for each permission group
    document.querySelectorAll('.parent-checkbox').forEach(function(parentCheckbox) {
        parentCheckbox.addEventListener('change', function() {
            const group = parentCheckbox.getAttribute('data-group');
            const childCheckboxes = document.querySelectorAll('.child-checkbox[data-group="' + group + '"]');

            childCheckboxes.forEach(function(childCheckbox) {
                childCheckbox.checked = parentCheckbox.checked;
            });
        });
    });

    // Handle the state of the "Check All" checkbox based on individual child checkbox selection
    document.querySelectorAll('.child-checkbox').forEach(function(childCheckbox) {
        childCheckbox.addEventListener('change', function() {
            const group = childCheckbox.getAttribute('data-group');
            const parentCheckbox = document.querySelector('.parent-checkbox[data-group="' + group + '"]');
            const allChildCheckboxes = document.querySelectorAll('.child-checkbox[data-group="' + group + '"]');
            const allChecked = Array.from(allChildCheckboxes).every(checkbox => checkbox.checked);

            parentCheckbox.checked = allChecked;
        });
    });

</script>
@endsection
