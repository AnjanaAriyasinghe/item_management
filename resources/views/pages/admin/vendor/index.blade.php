@extends('layouts.main')

@section('title', 'Vendors')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Vendors')
@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel"><span id="model-main-title"></span> Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitForm" method="POST" action="{{route('admin.vendors.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row g-4">
                        <div class="col-md-6" id="vendor_code_div" style="display:none">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="vendor_code" name="vendor_code" readonly placeholder="">
                                <label for="vendor_code">Vendor Code</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="name" name="name" placeholder="">
                                <label for="name">Full Name <span class="text-danger"> *</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="email" name="email" placeholder="">
                                <label for="name">Email </label>
                            </div>
                        </div>
                        <div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="nic_radio" name="custom_selection" class="form-check-input" checked value="1">
                                <label class="form-check-label" for="nic_radio">NIC</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="br_radio" name="custom_selection" class="form-check-input" value="0">
                                <label class="form-check-label" for="br_radio">BR</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="nic_br" name="nic" placeholder="">
                                <label for="name" id="custom_selection_lbl">NIC </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="phone" name="phone" placeholder="">
                                <label for="name">Contact No </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="mobile" name="mobile" placeholder="">
                                <label for="name">Mobile No </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="address" style="height: 100px" name="address"></textarea>
                                <label for="floatingTextarea">Address </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="remark" style="height: 100px" name="remark"></textarea>
                                <label for="floatingTextarea">Remark </label>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <h5>"The vendor has multiple account numbers. Please use the options below."</h5>
                            <div class="col-md-12">
                                <div id="account-numbers-section">
                                    <!-- Rows will be dynamically added here -->
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

<div class="modal fade" id="approvalModel" tabindex="-1" role="dialog" aria-labelledby="approvalModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitFormBtnApproval" method="POST" action="{{route('admin.vendor.approved')}}">
                    @csrf
                    <input type="hidden" name="id" id="data_value">
                    <div class="form-group row g-4">
                        <div class="col-md-12">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="approval_comment" style="height: 100px" name="approval_comment"></textarea>
                                <label for="floatingTextarea">Comment</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-close-new" data-bs-dismiss="modal">Close</button>
                <button type="button" id="submitFormBtnApprovalBtn" class="btn btn-primary save-button">Save</button>
                <div class="spinner-border_1 text-primary" role="status" style="display: none"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectModel" tabindex="-1" role="dialog" aria-labelledby="rejectModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Reject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitFormBtnReject" method="POST" action="{{route('admin.vendor.rejected')}}">
                    @csrf
                    <input type="hidden" name="id" id="data_value_reject">
                    <div class="form-group row g-4">
                        <div class="col-md-12">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="reject_comment" style="height: 100px" name="reject_comment"></textarea>
                                <label for="floatingTextarea">Comment</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-close-new" data-bs-dismiss="modal">Close</button>
                <button type="button" id="submitFormBtnRejectBtn" class="btn btn-primary save-button">Save</button>
                <div class="spinner-border_3 text-primary" role="status" style="display: none"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Vendors</h4>
            </div>
            <div class="card-body">
                @can('admin-common-vendor-create')
                <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal" data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i> Create Vendor</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>NIC</th>
                                <th>BR NO</th>
                                <th>Email</th>
                                <th>Contact No</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Approved Date</th>
                                <th>Rejected By</th>
                                <th>Rejected Date</th>
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
    $(document).on('click', '.btn-add-new', function(e) {
        initializeForm(null);;
    });
    $(function() {
        var table = $('.data-table').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">'
            , buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
            , processing: true
            , serverSide: true
            , ajax: "{{ route('admin.vendors.index')}}"
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }
                , {
                    data: 'vendor_code'
                    , name: 'vendor_code'
                }
                , {
                    data: 'name'
                    , name: 'name'
                }
                , {
                    data: 'nic'
                    , name: 'nic'
                },
                 {
                    data: 'br_no'
                    , name: 'br_no'
                },{
                    data: 'email'
                    , name: 'email'
                }
                , {
                    data: 'phone'
                    , name: 'phone'
                }
                , {
                    data: 'mobile'
                    , name: 'mobile'
                }, {
                    data: 'address'
                    , name: 'address'
                }
                , {
                    data: 'created_at'
                    , name: 'created_at'
                    , render: function(data) {
                        // Convert the data to a Date object and format it
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US'); // You can customize the locale
                    }
                },{
                    data:'status',
                    name:'status',
                },{
                    data:'approved_by',
                    name:'approved_by',
                },{
                    data:'approved',
                    name:'approved',
                },{
                    data:'rejected_by',
                    name:'rejected_by',
                },{
                    data:'rejected',
                    name:'rejected',
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

    $(document).on('click', '.btnView', function() {
        var id = $(this).data('id');
        $('#vendor_code_div').show();
        $('#add_remove').hide();
        $.ajax({
            url: '/admin/vendors/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#vendor_code').val(response.vendor.vendor_code);
                $('#name').val(response.vendor.name);
                $('#email').val(response.vendor.email);
                if (response.vendor.nic) {
                    $('#nic_radio').click();
                    $('#nic_br').val(response.vendor.nic);
                } else {
                    $('#br_radio').click();
                    $('#nic_br').val(response.vendor.br_no);
                }
                $('#phone').val(response.vendor.phone);
                $('#address').val(response.vendor.address);
                $('#remark').val(response.vendor.remark);
                $('#mobile').val(response.vendor.mobile);
                $('#createModel').modal('show');
                initializeForm(response.vendor.accounts);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
        $('#add_remove').show();
        $('#vendor_code_div').show();
        $('#submitForm').attr('action', "{{ route('admin.vendors.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');

        $.ajax({
            url: '/admin/vendors/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#vendor_code').val(response.vendor.vendor_code);
                $('#name').val(response.vendor.name);
                $('#email').val(response.vendor.email);
                if (response.vendor.nic) {
                    $('#nic_radio').click();
                    $('#nic_br').val(response.vendor.nic);
                } else {
                    $('#br_radio').click();
                    $('#nic_br').val(response.vendor.br_no);
                }
                $('#phone').val(response.vendor.phone);
                $('#address').val(response.vendor.address);
                $('#remark').val(response.vendor.remark);
                $('#mobile').val(response.vendor.mobile);
                $('#createModel').modal('show');
                initializeForm(response.vendor.accounts);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

    $(document).on('change', '#nic_radio', function() {
        $('#custom_selection_lbl').text('NIC');
        $('#nic_br').attr('name', 'nic');
    });

    $(document).on('change', '#br_radio', function() {
        $('#custom_selection_lbl').text('BR');
        $('#nic_br').attr('name', 'br_no');
    });

    function populateFields(accounts = []) {
        const $section = $('#account-numbers-section');
        $section.empty(); // Clear existing rows

        if (accounts.length > 0) {
            // Populate with existing data for editing
            accounts.forEach(account => {
                const accountMobileRow = createRow(account.account_number, account.mobile);
                $section.append(accountMobileRow);
            });
        }

        // Always ensure at least one empty row exists
        if ($section.children().length === 0) {
            addEmptyRow();
        }
    }

    // Function to create a single row with account and mobile inputs
    function createRow(accountNumber = '', mobileNumber = '') {
        return `
        <div class="input-group mb-2 account-mobile-row">
            <div class="col-md-4 form-group">
                <input type="text" class="form-control" name="account_numbers[]" value="${accountNumber}" placeholder="Enter account number">
            </div>
            <div class="col-md-4 form-group">
                <input type="tel" class="form-control mx-2" name="mobile_numbers[]" value="${mobileNumber}" placeholder="Enter mobile number" pattern="[0-9]{10}">
            </div>
            <div class="col-md-2 form-group" style="margin-left: 12px; margin-top: 3px" id="add_remove">
                <button type="button" class="btn btn-success add-account-mobile-row">+</button>
                <button type="button" class="btn btn-danger remove-account-mobile-row">-</button>
            </div>
        </div>`;
    }

    // Function to add an empty row
    function addEmptyRow() {
        const emptyRow = createRow();
        $('#account-numbers-section').append(emptyRow);
    }

    // Add a new row dynamically when the "+" button is clicked
    $(document).on('click', '.add-account-mobile-row', function() {
        addEmptyRow();
    });

    // Remove a row when the "-" button is clicked
    $(document).on('click', '.remove-account-mobile-row', function() {
        const $section = $('#account-numbers-section');

        // Remove the clicked row, but ensure at least one row remains
        if ($section.children().length > 1) {
            $(this).closest('.account-mobile-row').remove();
        }
    });

    // Initialize the form for Create or Edit mode
    function initializeForm(data = null) {
        if (data && Array.isArray(data)) {
            populateFields(data); // Edit mode with provided data
        } else {
            populateFields(); // Create mode with no data
        }
    }
    initializeForm();

</script>
@endsection
