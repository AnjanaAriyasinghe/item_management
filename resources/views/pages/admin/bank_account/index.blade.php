@extends('layouts.main')

@section('title', 'Bank Accounts')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Bank Accounts')
@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel"> <span id="model-main-title"></span> Bank Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitForm" method="POST" action="{{route('admin.bank_accounts.store')}}">
                    @csrf
                    <div class="form-group row g-4">
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select id="company_id" class="form-control category_id form-control-custom-select" name="company_id">
                                    <option value="">Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                <label for="name"> Company <span class="text-danger">*</span></label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="bank_id" id="bank_id" class="form-control bank_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Bank <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="branch_id" id="branch_id" class="form-control branch_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Branch <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="account_name" name="account_name" placeholder="">
                                <label for="account_name">Account Name<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="account_no" name="account_no" placeholder="">
                                <label for="account_no">Account No <span class="text-danger">*</span></label>
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
                <h4>Bank Accounts</h4>
            </div>
            <div class="card-body">
                @can('admin-common-bank_account-create')
                <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal" data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i> Create Account</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Company</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>Bank Name</th>
                                <th>Branch Name</th>
                                <th>Company</th>
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
    $(function() {
        var table = $('.data-table').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">'
            , buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
            , processing: true
            , serverSide: true
            , ajax: "{{ route('admin.bank_accounts.index')}}"
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }, {
                    data: 'company'
                    , name: 'company'
                }
                ,{
                    data: 'account_name'
                    , name: 'account_name'
                }
                , {
                    data: 'account_no'
                    , name: 'account_no'
                },{
                    data: 'bank.bank_name'
                    , name: 'bank.bank_name'
                }
                , {
                    data: 'branch.bank_branch_name'
                    , name: 'branch.bank_branch_name'
                }, {
                    data: 'company'
                    , name: 'company'
                },{
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
        , });
    });
    $(document).ready(function() {
        $.ajax({
            url: '/admin/getBanks'
            , method: 'GET'
            , success: function(response) {
                $('#bank_id').empty();
                $('#branch_id').empty();
                $("#bank_id ").append($("<option />"));
                $.each(response, function(key, bank) {
                    $("#bank_id").append($("<option />")
                        .val(bank.id)
                        .text(bank.bank_name + " - " + bank.bank_code));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('change', '.bank_id', function() {
        let id = $(this).val();
        $('#branch_id').empty();
        $.ajax({
            url: '/admin/getBranches/' + id
            , method: 'GET'
            , success: function(response) {
                $('#branch_id').empty();
                $("#branch_id ").append($("<option />"));
                $.each(response, function(key, branch) {
                    $("#branch_id").append($("<option />")
                        .val(branch.id)
                        .text(branch.bank_branch_name + " - " + branch.bank_branch_code));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
        $('#submitForm').attr('action', "{{ route('admin.bank_accounts.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/admin/bank_accounts/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#bank_id').val(response.bankAccount.bank_id).trigger('change');
                setTimeout(function() {
                    $('#branch_id').val(response.bankAccount.branch_id).trigger('change');
                }, 500);
                $('#account_name').val(response.bankAccount.account_name);
                $('#account_no').val(response.bankAccount.account_no);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

</script>
@endsection
