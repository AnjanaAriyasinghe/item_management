@extends('layouts.main')

@section('title', 'Cheque Books')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Cheque Books')
@section('content')
<style>
    .text-left {
    text-align: left;
}

</style>
<!-- [ Main Content ] start -->
<div class="modal fade" id="approvalModel" tabindex="-1" role="dialog" aria-labelledby="approvalModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitFormBtnApproval" method="POST" action="{{route('admin.cheque_books.approval')}}">
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
                <form id="submitFormBtnReject" method="POST" action="{{route('admin.cheque_books.reject')}}">
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
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel"> <span id="model-main-title"></span> Cheque Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitForm" method="POST" action="{{route('admin.cheque_books.store')}}">
                    @csrf
                    <div class="form-group row g-4">
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="bank_account_id" id="bank_account_id" class="form-control bank_account_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Bank Account <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="account_number" name="account_number" readonly placeholder="">
                                <label for="account_number">Account Number<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="number" id="number_of_cheque" name="number_of_cheque" placeholder="">
                                <label for="number_of_cheque">Number Of Cheque <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="number" id="start_number" name="start_number" placeholder="">
                                <label for="start_number">Start Number <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="nikname" name="nikname" placeholder="">
                                <label for="nikname">Nikname <span class="text-danger">*</span></label>
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
<div class="modal fade" id="viewModel" tabindex="-1" role="dialog" aria-labelledby="viewModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModelLabel"> <span id="model-main-title_view"></span> Cheque Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm view_data" id="pc-dt-simple">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td>:</td>
                                    <td id="view_in_id"></td>
                                    <th>Account Name</th>
                                    <td>:</td>
                                    <td id="view_bank_account"></td>
                                </tr>
                                <tr>
                                    <th>Account Number</th>
                                    <td>:</td>
                                    <td id="view_account_number"></td>
                                    <th>Bank Name</th>
                                    <td>:</td>
                                    <td id="view_bank_name"></td>
                                </tr>
                                <tr>
                                    <th>Branch Name</th>
                                    <td>:</td>
                                    <td id="view_branch_name"></td>
                                    <th>No of Cheques</th>
                                    <td>:</td>
                                    <td id="view_number_of_cheque"></td>
                                </tr>
                                <tr>
                                    <th>Start Number</th>
                                    <td>:</td>
                                    <td id="view_start_number"></td>
                                    <th>End Number</th>
                                    <td>:</td>
                                    <td id="view_end_number"></td>
                                </tr>
                                <tr>
                                    <th>Approved By</th>
                                    <td>:</td>
                                    <td id="view_approved_user"></td>
                                    <th>Approved Comment</th>
                                    <td>:</td>
                                    <td id="view_approval_comment"></td>
                                </tr>
                                <tr>
                                    <th>Approved Date</th>
                                    <td>:</td>
                                    <td id="view_approved_date"></td>
                                    <th>Rejected By</th>
                                    <td>:</td>
                                    <td id="view_reject_user"></td>
                                </tr>
                                <tr>
                                    <th>Rejected Comment</th>
                                    <td>:</td>
                                    <td id="view_reject_comment"></td>
                                    <th>Rejected Date</th>
                                    <td>:</td>
                                    <td id="view_rejected_date"></td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <td>:</td>
                                    <td id="view_created_by"></td>
                                    <th>Created At</th>
                                    <td>:</td>
                                    <td id="view_created_at"></td>
                                </tr>
                                <tr>
                                <tr>
                                    <th>Deleted By</th>
                                    <td>:</td>
                                    <td id="view_deleted_by"></td>
                                    <th>Deleted Date</th>
                                    <td>:</td>
                                    <td id="view_deleted_at"></td>
                                </tr>
                                <tr>
                                    <th>Nikname</th>
                                    <td>:</td>
                                    <td id="view_nikname"></td>
                                    <th>Status</th>
                                    <td>:</td>
                                    <td id="view_status"></td>
                                </tr>
                                <tr>
                                    <th>Book Code</th>
                                    <td>:</td>
                                    <th id="view_book_code"></th>
                                    <th></th>
                                    <td>:</td>
                                    <th ></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-close-new" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Cheque Books</h4>
            </div>
            <div class="card-body">
                @can('admin-common-cheque_book-create')
                <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal" data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i> Create Cheque Book</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Code</th>
                                <th>Ac Name</th>
                                <th>Nikname</th>
                                <th>Ac No</th>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>No of Cheques</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Approved By</th>
                                <th>Rejected By</th>
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
            , ajax: "{{ route('admin.cheque_books.index')}}"
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }
                , {
                    data: 'book_code'
                    , name: 'book_code'
                }
                , {
                    data: 'bank_account.account_name'
                    , name: 'bank_account.account_name'
                }
                , {
                    data: 'nikname'
                    , name: 'nikname'
                }
                , {
                    data: 'bank_account.account_no'
                    , name: 'bank_account.account_no'
                }, {
                    data: 'bank_account.bank.bank_name'
                    , name: 'bank_account.bank.bank_name'
                }
                , {
                    data: 'bank_account.branch.bank_branch_name'
                    , name: 'bank_account.branch.bank_branch_name'
                },
                {
                    data: 'number_of_cheque'
                    , name: 'number_of_cheque'
                },
                {
                    data: 'status'
                    , name: 'status'
                },
                {
                    data: 'created_user.name'
                    , name: 'created_user.name'
                }
                ,
                {
                    data: 'approved_user_name'
                    , name: 'approved_user_name'
                }
                ,
                {
                    data: 'reject_user_name'
                    , name: 'reject_user_name'
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
    $(document).ready(function() {
        $.ajax({
            url: '/admin/getAccounts'
            , method: 'GET'
            , success: function(response) {
                $('#bank_account_id').empty();
                $("#bank_account_id ").append($("<option />"));
                $.each(response.bankAccount, function(key, bank) {
                    $("#bank_account_id").append($("<option />")
                        .val(bank.id)
                        .text(bank.account_name + "  -  " + bank.account_no));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('change', '.bank_account_id', function() {
        let id = $(this).val();
        $.ajax({
            url: '/admin/getAccountNo/' + id
            , method: 'GET'
            , success: function(response) {
                $('#account_number').empty();
                $('#account_number').val(response);
            }
            , error: function(xhr) {
                var errorMessage = xhr.responseJSON.message; // Assuming the server sends an error message in the response
                    Swal.fire({
                        icon: "error"
                        , title: "Oops..."
                        , text: errorMessage
                    });

            }
        });
    });

    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
        $('#submitForm').attr('action', "{{ route('admin.cheque_books.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/admin/cheque_books/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#bank_account_id').val(response.chequeBook.bank_account_id).trigger('change');
                $('#account_number').val(response.chequeBook.account_number);
                $('#number_of_cheque').val(response.chequeBook.number_of_cheque);
                $('#start_number').val(response.chequeBook.start_number);
                $('#nikname').val(response.chequeBook.nikname);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('click', '.btnView', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/cheque_books/' + id
            , method: 'GET'
            , success: function(response) {
                $('#view_data').empty();
                $('#view_in_id').text(response.chequeBook.id);
                $('#view_bank_account').text(response.chequeBook.bank_account.account_name);
                $('#view_account_number').text(response.chequeBook.account_number);
                $('#view_bank_name').text(response.chequeBook.bank_account.bank.bank_name);
                $('#view_branch_name').text(response.chequeBook.bank_account.branch.bank_branch_name);
                $('#view_number_of_cheque').text(response.chequeBook.number_of_cheque);
                $('#view_start_number').text(response.chequeBook.start_number);
                $('#view_end_number').text(response.chequeBook.end_number);
                $('#view_approved_user').text(response.chequeBook.approved_user_name?response.chequeBook.approved_user_name.name:'-');
                $('#view_approval_comment').text(response.chequeBook.approval_comment??'-');
                $('#view_approved_date').text(response.chequeBook?.approved_date ? new Date(response.chequeBook.approved_date).toLocaleDateString('en-GB') : '-');
                $('#view_reject_user').text(response.chequeBook.reject_user_name?response.chequeBook.reject_user_name.name:'-');
                $('#view_reject_comment').text(response.chequeBook.reject_comment??'-');
                $('#view_rejected_date').text(response.chequeBook?.rejected_date ? new Date(response.chequeBook.rejected_date).toLocaleDateString('en-GB') : '-');
                $('#view_created_by').text(response.chequeBook.created_user.name);
                $('#view_created_at').text(response.chequeBook?.created_at ? new Date(response.chequeBook.created_at).toLocaleDateString('en-GB') : '-');
                $('#view_deleted_by').text(response.chequeBook.deleted_by?response.chequeBook.deleted_by.name:'-');
                $('#view_deleted_at').text(response.chequeBook?.deleted_at ? new Date(response.chequeBook.deleted_at).toLocaleDateString('en-GB') : '-');
                $('#view_book_code').text(response.chequeBook.book_code);
                $('#view_nikname').text(response.chequeBook.nikname);

                const status = response.chequeBook.status;
                const capitalizedStatus = status.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                let badgeClass;
                if (status === 'pending') {
                    badgeClass = 'badge bg-warning';
                } else if (status === 'approved') {
                    badgeClass = 'badge bg-primary';
                } else if (status === 'reject') {
                    badgeClass = 'badge bg-danger';
                } else {
                    badgeClass = 'badge bg-secondary';
                }
                $('#view_status').html(`<span class="${badgeClass}" style="">${capitalizedStatus}</span>`);

            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
</script>
@endsection
