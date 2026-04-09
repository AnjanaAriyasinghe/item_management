@extends('layouts.main')

@section('title', 'Company')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Company')
@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel"><span id="model-main-title"></span> Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitForm" method="POST" action="{{route('admin.company.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row g-4">
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="system_title" name="system_title" placeholder="">
                                <label for="name">System title <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="name" name="name" placeholder="">
                                <label for="name">Company name <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="description" name="description" placeholder="">
                                <label for="name">Description <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-md-6" id="password_div">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="contact_number" name="contact_number" placeholder="">
                                <label for="name">Contact number <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6" id="password_confirmation_div">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="mobile" name="mobile" placeholder="">
                                <label for="name">Mobile number<span class="text-danger"> *</span></label>
                            </div>
                        </div>
                        <div class="col-md-6" id="password_confirmation_div">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="pv_no" name="pv_no" placeholder="">
                                <label for="name">pv_no<span class="text-danger"> *</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="address" style="height: 100px" name="address"></textarea>
                                <label for="floatingTextarea">Address <span class="text-danger"> *</span></label>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="roles" id="role" class="form-control role_id form-control-custom-select">
                                    <option value=""></option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                                <label for="name"> Role <span class="text-danger">*</span></label>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="custom-file-upload">
                                <label for="image" class="custom-file-label">
                                    <i class="fas fa-upload"></i> Upload Logo
                                </label>
                                <input type="file" id="logo" name="logo" accept="image/*" class="form-control-file" onchange="previewImage(event)">
                            </div>
                            <div id="image-preview" class="mt-3">
                                <div class="preview-container">
                                    <img id="preview-img" class="hidden" />
                                    <button type="button" id="remove-btn" class="remove-btn hidden" onclick="removeImage()">x</button>
                                </div>
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
                <h4>Company</h4>
            </div>
            <div class="card-body">
                @can('admin-common-company-create')
                    <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal" data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i> Add company</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>logo</th>
                                <th>Name</th>
                                <th>System Title</th>
                                <th>Description</th>
                                <th>Address</th>
                                <th>Contact Number</th>
                                <th>mobile</th>
                                <th>updated_By</th>
                                <th>updated_at</th>
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
            , ajax: "{{ route('admin.company.index')}}"
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }
                , {
                    data: 'logo'
                    , name: 'logo'
                }, {
                    data: 'name'
                    , name: 'name'
                }
                , {
                    data: 'system_title'
                    , name: 'system_title'
                }, {
                    data: 'description'
                    , name: 'description'
                }
                , {
                    data: 'address'
                    , name: 'address'
                },
                {
                    data: 'contact_number'
                    , name: 'contact_number'
                },
                {
                    data: 'mobile'
                    , name: 'mobile'
                },
                {
                    data: 'updated_by.name'
                    , name: 'updated_by.name'
                }
                , {
                    data: 'updated_at'
                    , name: 'updated_at'
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
        $('#submitForm').attr('action', "{{ route('admin.company.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/admin/company/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#system_title').val(response.system_title);
                $('#name').val(response.name);
                $('#contact_number').val(response.contact_number);
                $('#mobile').val(response.mobile);
                $('#description').val(response.description);
                $('#address').val(response.address);
                $('#pv_no').val(response.pv_no);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

</script>
@endsection
