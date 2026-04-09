@extends('layouts.main')

@section('title', 'Users')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Users')
<style>
    .image-zoom-container {
        width: 50px;
        height: 50px;
        overflow: hidden;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .enlarge-on-hover {
        transition: transform 0.3s ease-in-out;
        width: 100%;
        height: 50%;
        object-fit: cover;
    }

    .enlarge-on-hover:hover {
        transform: scale(3);
        z-index: 1;
        position: absolute;
    }
</style>
<style>
    .custom-file-upload {
        position: relative;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        text-align: center;
        cursor: pointer;
    }

    .custom-file-label {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .custom-file-label:hover {
        background-color: #0056b3;
    }

    #image {
        display: none;
    }

    .preview-container {
        position: relative;
        display: inline-block;
        cursor: zoom-in;
    }

    #preview-img {
        max-width: 100%;
        max-height: 300px;
        border-radius: 10px;
        margin-top: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    #preview-img:hover {
        transform: scale(2.1);
        /* Zoom effect */
    }

    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        background-color: #e74c3c;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .remove-btn:hover {
        background-color: #c0392b;
    }

    .hidden {
        display: none;
    }
</style>
<style>
    @import url('https://fonts.googleapis.com/css?family=Quicksand:400,500,700&subset=latin-ext');

    /* body {
    font-family: 'Quicksand', sans-serif;
    font-weight: 450;
    min-height: 100vh;
    color: #ADAFB6;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #fff;
    transition: all .2s ease;
} */

    .multiSelect-dropdown {
        /* width: 300px; */
        position: relative;
    }

    .multiSelect-dropdown *,
    .multiSelect-dropdown *::before,
    .multiSelect-dropdown *::after {
        box-sizing: border-box;
    }

    .multiSelect-dropdown_dropdown {
        font-size: 14px;
        min-height: 35px;
        line-height: 35px;
        border-radius: 8px;
        box-shadow: none;
        outline: none;
        background-color: #fff;
        color: #444f5b;
        border: 1px solid #d9dbde;
        font-weight: 400;
        padding: 0.5px 13px;
        margin: 0;
        transition: .1s border-color ease-in-out;
        cursor: pointer;
    }

    .multiSelect-dropdown_dropdown.-hasValue {
        padding: 5px 30px 5px 5px;
        cursor: default;
    }

    .multiSelect-dropdown_dropdown.-open {
        box-shadow: none;
        outline: none;
        padding: 4.5px 29.5px 4.5px 4.5px;
        border: 1.5px solid #4073FF;
    }

    .multiSelect-dropdown_arrow::before,
    .multiSelect-dropdown_arrow::after {
        content: '';
        position: absolute;
        display: block;
        width: 2px;
        height: 8px;
        border-radius: 20px;
        border-bottom: 8px solid #99A3BA;
        top: 40%;
        transition: all .15s ease;
    }

    .multiSelect-dropdown_arrow::before {
        right: 18px;
        -webkit-transform: rotate(-50deg);
        transform: rotate(-50deg);
    }

    .multiSelect-dropdown_arrow::after {
        right: 13px;
        -webkit-transform: rotate(50deg);
        transform: rotate(50deg);
    }

    .multiSelect-dropdown_list {
        margin: 0;
        margin-bottom: 25px;
        padding: 0;
        list-style: none;
        opacity: 0;
        visibility: hidden;
        position: absolute;
        max-height: calc(10 * 31px);
        top: 28px;
        left: 0;
        z-index: 9999;
        right: 0;
        background: #fff;
        border-radius: 4px;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-transform-origin: 0 0;
        transform-origin: 0 0;
        transition: opacity 0.1s ease, visibility 0.1s ease, -webkit-transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32);
        transition: opacity 0.1s ease, visibility 0.1s ease, transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32);
        transition: opacity 0.1s ease, visibility 0.1s ease, transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32), -webkit-transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32);
        -webkit-transform: scale(0.8) translate(0, 4px);
        transform: scale(0.8) translate(0, 4px);
        border: 1px solid #d9dbde;
        box-shadow: 0px 10px 20px 0px rgba(0, 0, 0, 0.12);
    }

    .multiSelect-dropdown_option {
        margin: 0;
        padding: 0;
        opacity: 0;
        -webkit-transform: translate(6px, 0);
        transform: translate(6px, 0);
        transition: all .15s ease;
    }

    .multiSelect-dropdown_option.-selected {
        display: none;
    }

    .multiSelect-dropdown_option:hover .multiSelect-dropdown_text {
        color: #fff;
        background: #4d84fe;
    }

    .multiSelect-dropdown_text {
        cursor: pointer;
        display: block;
        padding: 5px 13px;
        color: #525c67;
        font-size: 14px;
        text-decoration: none;
        outline: none;
        position: relative;
        transition: all .15s ease;
    }

    .multiSelect-dropdown_list.-open {
        opacity: 1;
        visibility: visible;
        -webkit-transform: scale(1) translate(0, 12px);
        transform: scale(1) translate(0, 12px);
        transition: opacity 0.15s ease, visibility 0.15s ease, -webkit-transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32);
        transition: opacity 0.15s ease, visibility 0.15s ease, transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32);
        transition: opacity 0.15s ease, visibility 0.15s ease, transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32), -webkit-transform 0.15s cubic-bezier(0.4, 0.6, 0.5, 1.32);
    }

    .multiSelect-dropdown_list.-open+.multiSelect-dropdown_arrow::before {
        -webkit-transform: rotate(-130deg);
        transform: rotate(-130deg);
    }

    .multiSelect-dropdown_list.-open+.multiSelect-dropdown_arrow::after {
        -webkit-transform: rotate(130deg);
        transform: rotate(130deg);
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option {
        opacity: 1;
        -webkit-transform: translate(0, 0);
        transform: translate(0, 0);
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(1) {
        transition-delay: 10ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(2) {
        transition-delay: 20ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(3) {
        transition-delay: 30ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(4) {
        transition-delay: 40ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(5) {
        transition-delay: 50ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(6) {
        transition-delay: 60ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(7) {
        transition-delay: 70ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(8) {
        transition-delay: 80ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(9) {
        transition-delay: 90ms;
    }

    .multiSelect-dropdown_list.-open .multiSelect-dropdown_option:nth-child(10) {
        transition-delay: 100ms;
    }

    .multiSelect-dropdown_choice {
        background: rgba(77, 132, 254, 0.1);
        color: #444f5b;
        padding: 4px 8px;
        line-height: 17px;
        margin: 5px;
        display: inline-block;
        font-size: 13px;
        border-radius: 30px;
        cursor: pointer;
        font-weight: 500;
    }

    .multiSelect-dropdown_deselect {
        width: 12px;
        height: 12px;
        display: inline-block;
        stroke: #b2bac3;
        stroke-width: 4px;
        margin-top: -1px;
        margin-left: 2px;
        vertical-align: middle;
    }

    .multiSelect-dropdown_choice:hover .multiSelect-dropdown_deselect {
        stroke: #a1a8b1;
    }

    .multiSelect-dropdown_noselections {
        text-align: center;
        padding: 7px;
        color: #b2bac3;
        font-weight: 450;
        margin: 0;
    }

    .multiSelect-dropdown_placeholder {
        position: absolute;
        left: 8px;
        font-size: 14px;
        top: 8px;
        padding: 0 4px;
        background-color: #fff;
        /* color: #b8bcbf; */
        pointer-events: none;
        transition: all .1s ease;
    }

    .multiSelect-dropdown_dropdown.-open+.multiSelect-dropdown_placeholder,
    .multiSelect-dropdown_dropdown.-open.-hasValue+.multiSelect-dropdown_placeholder {
        top: -11px;
        left: 17px;
        color: #4073FF;
        font-size: 13px;
    }

    .multiSelect-dropdown_dropdown.-hasValue+.multiSelect-dropdown_placeholder {
        top: -11px;
        left: 17px;
        color: #6e7277;
        font-size: 13px;
    }
</style>
@section('content')

    <!-- [ Main Content ] start -->
    <div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModelLabel"><span id="model-main-title"></span> User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitForm" method="POST" action="{{ route('admin.users.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row g-4">
                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="text" id="name"
                                        name="name" placeholder="">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="text" id="email"
                                        name="email" placeholder="">
                                    <label for="name">Email <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="text" id="mobile"
                                        name="mobile" placeholder="">
                                    <label for="name">Contact No <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="text" id="nic"
                                        name="nic" placeholder="">
                                    <label for="name">NIC</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="password_div">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="password" id="password"
                                        name="password" placeholder="">
                                    <label for="name">Password <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6" id="password_confirmation_div">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="password"
                                        id="password_confirmation" name="password_confirmation" placeholder="">
                                    <label for="name">Confirm Password <span class="text-danger"> *</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-floating">
                                    <textarea class="form-control" id="address" style="height: 100px" name="address"></textarea>
                                    <label for="floatingTextarea">Address <span class="text-danger"> *</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="multiSelect-dropdown">
                                    <div class="form-group form-floating">
                                        <select multiple
                                            class="form-control form-control-custom-select multiSelect-dropdown_field"
                                            name="companies[]" id="companies" data-placeholder="Companies">
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    data-company-name="{{ $company->name }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <select name="roles" id="role"
                                        class="form-control role_id form-control-custom-select">
                                        <option value=""></option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="name"> Role <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-floating mb-0">
                                    <select class="form-control role_id form-control-custom-select" name="default_company"
                                        id="default_company">
                                        <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="name"> Default Company <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                            <div class="multiSelect-dropdown">
                                <div class="form-group form-floating">
                                    <select class="form-control form-control-custom-select multiSelect-dropdown_field" name="companies[]" id="companies" data-placeholder="Add Companies">
                                        <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> --}}


                            <div class="col-md-6">
                                <div class="custom-file-upload">
                                    <label for="image" class="custom-file-label">
                                        <i class="fas fa-upload"></i> Upload Image
                                    </label>
                                    <input type="file" id="image" name="image" accept="image/*"
                                        class="form-control-file" onchange="previewImage(event)">
                                </div>
                                <div id="image-preview" class="mt-3">
                                    <div class="preview-container">
                                        <img id="preview-img" class="hidden" />
                                        <button type="button" id="remove-btn" class="remove-btn hidden"
                                            onclick="removeImage()">x</button>
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
    <div class="modal fade" id="passwordReset" tabindex="-1" role="dialog" aria-labelledby="passwordResetLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordResetLabel"><span id="model-main-title"></span>Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitFormBtnApproval" method="POST" action="{{ route('admin.users.update_password') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row g-4">
                            <div class="col-md-6" id="password_div">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="password" id="password"
                                        name="password" placeholder="">
                                    <label for="name">Password <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6" id="password_confirmation_div">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="password"
                                        id="password_confirmation" name="password_confirmation" placeholder="">
                                    <label for="name">Confirm Password <span class="text-danger"> *</span></label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="user_id" name="user_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-close-new" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="submitFormBtnApprovalBtn"
                        class="btn btn-primary save-button">Save</button>
                    <div class="spinner-border_1 text-primary" role="status" style="display: none"></div>
                </div>
            </div>
        </div>
    </div>


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
                            Create User</i></button>
                    @endcan
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-sm data-table" id="pc-dt-simple">
                                <thead>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Default Company</th>
                                    <th>Company</th>
                                    <th>NIC</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
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
            $('#password_div').show();
            $('#password_confirmation_div').show();

            $('#companies').val(null).trigger('change');

            // var defaultCompanyDropdown = $('#default_company');
            // defaultCompanyDropdown.empty();
            $('#default_company').val(null).trigger('change');


            updateDefaultCompanyDropdown();
        });
        $(function() {
            var table = $('.data-table').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.users.index') }}",
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'image',
                    name: 'image'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'role',
                    name: 'role'
                }, {
                    data: 'default_company_name',
                    name: 'default_company_name'
                }, {
                    data: 'company',
                    name: 'company'
                }, {
                    data: 'nic',
                    name: 'nic'
                }, {
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'mobile',
                    name: 'mobile'
                }, {
                    data: 'address',
                    name: 'address'
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US'); // You can customize the locale
                    }
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
            });
        });

        $(document).on('click', '.btn-add-new', function() {
            $('#password_div').show();
            $('#password_confirmation_div').show();

            // $('#companies').val(null).trigger('change');
            // $('#companies').val(null);

            // Clear the companies multi-select field
            $('#companies').val(null).trigger('change');
            var $companiesMultiSelect = $('#companies').closest('.multiSelect-dropdown');
            $companiesMultiSelect.find('.multiSelect-dropdown_dropdown').removeClass('-hasValue').empty();
            $companiesMultiSelect.find('.multiSelect-dropdown_list .multiSelect-dropdown_option').removeClass(
                '-selected');


            updateDefaultCompanyDropdown();
        });

        $(document).on('click', '.btnEdit', function() {
            $('#password_div').hide();
            $('#password_confirmation_div').hide();
            var id = $(this).data('id');
            $('#submitForm').attr('action', "{{ route('admin.users.update', '') }}/" + id);
            $('#submitForm').append('<input type="hidden" name="_method" value="put">');
            $.ajax({
                url: '/admin/users/' + id + '/edit',
                method: 'GET',
                success: function(response) {
                    $('#name').val(response.user_data.name);
                    $('#email').val(response.user_data.email);
                    $('#mobile').val(response.user_data.mobile);
                    $('#nic').val(response.user_data.nic);
                    $('#address').val(response.user_data.address);
                    $('.role_id').val(response.user.pivot.role_id).trigger('change');
                    $('#default_company').val(response.user_data.default_company).trigger('change');


                    // const selectedCompanies = response.user_companies.map(company => company);
                    // console.log(selectedCompanies);
                    // $('#companies').val(selectedCompanies).trigger('change');

                    //?In the following loop element from hidden option list and bootstrap option list is being seleceted for each id
                    //?then manages what need to be displayed in the custom dropdown UI by syncing both components on change


                    const selectedCompanyIds = response.user_companies.map(company => company);
                    console.log('selectedCompanyIds for setting:', response.user_companies);

                    $('#companies').val(selectedCompanyIds);

                    var $companiesMultiSelect = $('#companies').closest('.multiSelect-dropdown');
                    var $dropdown = $companiesMultiSelect.find('.multiSelect-dropdown_dropdown');
                    var $list = $companiesMultiSelect.find('.multiSelect-dropdown_list');
                    var $optionsInList = $list.find('.multiSelect-dropdown_option');


                    $dropdown.find('.multiSelect-dropdown_choice')
                        .remove(); // Remove existing displayed choices
                    $optionsInList.removeClass('-selected');
                    $dropdown.removeClass('-hasValue');

                    selectedCompanyIds.forEach(function(companyId) {
                        var $originalOption = $('#companies option[value="' + companyId + '"]');
                        var $customOptionLi = $list.find(
                            '.multiSelect-dropdown_option[data-value="' + companyId + '"]');

                        if ($originalOption.length && $customOptionLi.length) {
                            $customOptionLi.addClass('-selected');

                            $dropdown.append(function() {
                                return $('<span class="multiSelect-dropdown_choice">' +
                                    $originalOption.text() +
                                    '<svg class="multiSelect-dropdown_deselect"></svg><i class="ph-duotone ph-x-circle"></i></span>'
                                ).click(function(e) {
                                    var selfChoice = $(this);
                                    e.stopPropagation();
                                    selfChoice.remove();
                                    var deselectedText = selfChoice.text();
                                    var deselectedOriginalOption = $(
                                        '#companies').find(
                                        'option[data-company-name="' +
                                        deselectedText + '"]');
                                    if (!deselectedOriginalOption.length) {
                                        deselectedOriginalOption = $(
                                            '#companies').find(
                                            'option:contains(' +
                                            deselectedText + ')');
                                    }
                                    var deselectedValue =
                                        deselectedOriginalOption.val();

                                    deselectedOriginalOption.prop('selected',
                                        false);
                                    $list.find(
                                        '.multiSelect-dropdown_option[data-value="' +
                                        deselectedValue + '"]').removeClass(
                                        '-selected');

                                    if ($dropdown.children(':visible')
                                        .length === 0) {
                                        $dropdown.removeClass('-hasValue');
                                    }
                                    $('#companies').trigger('change');
                                });
                            }).addClass('-hasValue');
                        }
                    });

                    $('#companies').trigger('change');

                    updateDefaultCompanyDropdown();

                    $('#createModel').modal('show');

                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
        });

        $(document).on('click', '.btnUpdate', function() {
            var id = $(this).data('id');
            $('#user_id').val(id);
            $('#submitForm').attr('action', "{{ route('admin.users.update_password') }}");
            $('#submitForm').append('<input type="hidden" name="_method" value="post">');
        });

        function updateDefaultCompanyDropdown() {
            var selectedCompanyIds = $('#companies').val();
            var defaultCompanyDropdown = $('#default_company');
            defaultCompanyDropdown.empty();

            if (selectedCompanyIds && selectedCompanyIds.length > 0) {
                selectedCompanyIds.forEach(function(companyId) {
                    var companyName = $('#companies option[value="' + companyId + '"]').data('company-name');
                    defaultCompanyDropdown.append($('<option>', {
                        value: companyId,
                        text: companyName
                    }));
                });
            } else {
                // If no companies are selected, add the "Please select..." option
                defaultCompanyDropdown.append($('<option>', {
                    value: '',
                    text: 'Please select one or more companies first'
                }));
            }

            defaultCompanyDropdown.trigger('change');
        }

        // Attach the update function to the change event of the 'companies' multi-select
        $(document).on('change', '#companies', updateDefaultCompanyDropdown);
    </script>
    <script>
        function previewImage(event) {
            const imageFile = event.target.files[0];
            if (!imageFile) return;
            if (!imageFile.type.startsWith("image/")) {
                alert("Please upload a valid image file.");
                return;
            }
            const previewImg = document.getElementById("preview-img");
            const removeBtn = document.getElementById("remove-btn");
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove("hidden");
                removeBtn.classList.remove("hidden");
            };
            reader.readAsDataURL(imageFile);
        }

        function removeImage() {
            const previewImg = document.getElementById("preview-img");
            const removeBtn = document.getElementById("remove-btn");
            const fileInput = document.getElementById("image");
            previewImg.src = "";
            previewImg.classList.add("hidden");
            removeBtn.classList.add("hidden");
            fileInput.value = "";
        }
    </script>
    <script>
        jQuery(function() {
            jQuery('.multiSelect-dropdown').each(function(e) {
                var self = jQuery(this);
                var field = self.find('.multiSelect-dropdown_field'); // This is your original <select>
                var fieldOption = field.find('option');
                var placeholder = field.attr('data-placeholder');

                field.hide().after(`<div class="multiSelect-dropdown_dropdown"></div>
                            <span class="multiSelect-dropdown_placeholder">` + placeholder + `</span>
                            <ul class="multiSelect-dropdown_list"></ul>
                            <span class="multiSelect-dropdown_arrow"></span>`);

                fieldOption.each(function(e) {
                    jQuery('.multiSelect-dropdown_list', self).append(
                        `<li class="multiSelect-dropdown_option" data-value="` + jQuery(this)
                        .val() + `">
                                                    <a class="multiSelect-dropdown_text">` + jQuery(this).text() + `</a>
                                                  </li>`);
                });

                var dropdown = self.find('.multiSelect-dropdown_dropdown');
                var list = self.find('.multiSelect-dropdown_list');
                var option = self.find('.multiSelect-dropdown_option'); // These are the li elements
                var optionText = self.find('.multiSelect-dropdown_text');

                dropdown.attr('data-multiple', 'true');
                list.css('top', dropdown.height() + 5);

                // Handle selection within the custom multi-select UI
                option.click(function(e) {
                    var selfOption = jQuery(this); // Renamed to avoid conflict
                    e.stopPropagation();

                    var valueToSelect = selfOption.attr('data-value');
                    var originalOption = field.find('option[value="' + valueToSelect + '"]');

                    if (!selfOption.hasClass('-selected')) {
                        // Select the option
                        selfOption.addClass('-selected');
                        originalOption.prop('selected', true);
                        dropdown.append(function() {
                            return jQuery('<span class="multiSelect-dropdown_choice">' +
                                originalOption.text() +
                                '<svg class="multiSelect-dropdown_deselect pc-item -iconX"></svg><i class="ph-duotone ph-x-circle"></i></span>'
                            ).click(function(e) {
                                var selfChoice = jQuery(
                                    this); // Renamed to avoid conflict
                                e.stopPropagation();
                                selfChoice.remove();
                                var deselectedText = selfChoice.text();
                                var deselectedOriginalOption = field.find(
                                    'option:contains(' + deselectedText + ')');
                                var deselectedValue = deselectedOriginalOption
                                    .val();

                                deselectedOriginalOption.prop('selected', false);
                                list.find(
                                    '.multiSelect-dropdown_option[data-value="' +
                                    deselectedValue + '"]').removeClass(
                                    '-selected');

                                if (dropdown.children(':visible').length === 0) {
                                    dropdown.removeClass('-hasValue');
                                }
                                field.trigger(
                                    'change'
                                    ); // !!! IMPORTANT: Trigger change on original select
                            });
                        }).addClass('-hasValue');
                    }

                    list.css('top', dropdown.height() + 5);
                    if (!option.not('.-selected').length) {
                        list.append(
                            '<h5 class="multiSelect-dropdown_noselections">No Selections</h5>');
                    }
                    field.trigger('change'); // !!! IMPORTANT: Trigger change on original select
                });

                // Handle deselection from the displayed choices
                dropdown.on('click', '.multiSelect-dropdown_deselect', function(e) {
                    e.stopPropagation();
                    var selfDeselect = jQuery(this)
                        .parent(); // The parent is the multiSelect-dropdown_choice span
                    var deselectedText = selfDeselect.text();
                    var deselectedOriginalOption = field.find('option:contains(' + deselectedText +
                        ')');
                    var deselectedValue = deselectedOriginalOption.val();

                    selfDeselect.remove(); // Remove the visual choice
                    deselectedOriginalOption.prop('selected',
                        false); // Deselect in the original <select>
                    list.find('.multiSelect-dropdown_option[data-value="' + deselectedValue + '"]')
                        .removeClass('-selected'); // Update the option in the list

                    if (dropdown.children(':visible').length === 0) {
                        dropdown.removeClass('-hasValue');
                        list.find('.multiSelect-dropdown_noselections')
                            .remove(); // Remove "No Selections" if it was there
                    }
                    field.trigger('change'); // !!! IMPORTANT: Trigger change on original select
                });


                dropdown.click(function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    dropdown.toggleClass('-open');
                    list.toggleClass('-open').scrollTop(0).css('top', dropdown.height() + 5);
                });

                jQuery(document).on('click touch', function(e) {
                    if (dropdown.hasClass('-open') && !self.is(e.target) && self.has(e.target)
                        .length === 0) {
                        dropdown.removeClass('-open');
                        list.removeClass('-open');
                    }
                });

                // Initialize display based on current selected options in the original field
                field.find('option:selected').each(function() {
                    var selectedOption = jQuery(this);
                    dropdown.append(function() {
                        return jQuery('<span class="multiSelect-dropdown_choice">' +
                            selectedOption.text() +
                            '<svg class="multiSelect-dropdown_deselect pc-item -iconX"></svg><i class="ph-duotone ph-x-circle"></i></span>'
                        ).click(function(e) {
                            var selfChoice = jQuery(this);
                            e.stopPropagation();
                            selfChoice.remove();
                            var deselectedText = selfChoice.text();
                            var deselectedOriginalOption = field.find(
                                'option:contains(' + deselectedText + ')');
                            var deselectedValue = deselectedOriginalOption.val();

                            deselectedOriginalOption.prop('selected', false);
                            list.find('.multiSelect-dropdown_option[data-value="' +
                                deselectedValue + '"]').removeClass('-selected');

                            if (dropdown.children(':visible').length === 0) {
                                dropdown.removeClass('-hasValue');
                            }
                            field.trigger(
                                'change'); // Trigger change on original select
                        });
                    }).addClass('-hasValue');
                    list.find('.multiSelect-dropdown_option[data-value="' + selectedOption.val() +
                        '"]').addClass('-selected');
                });
                if (field.find('option:selected').length === 0) {
                    dropdown.removeClass('-hasValue');
                }
            });
        });
    </script>
@endsection
