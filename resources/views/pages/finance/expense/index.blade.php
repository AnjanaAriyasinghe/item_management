@extends('layouts.main')

@section('title', 'Expenses')
@section('breadcrumb-item', 'Finance')
@section('breadcrumb-item-active', 'Expenses')

<style>
    .custom-file-upload {
        position: relative;
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
        margin-top: 10px;
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
    }

    #image-preview-2:hover {
        transform: scale(2.1);
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
    #pdf {
        display: none;
    }

    #pdf-preview {
        display: flex;
        /* align-items: center; */
        margin-top: 10px;
        flex-direction: column;
    }

    #pdf-embed {
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .remove-btn {
        margin-top: 10px;
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
@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel"> <span id="model-main-title"></span> Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitForm" method="POST" action="{{route('finance.expenses.store')}}">
                    @csrf
                    <div class="form-group row g-4">
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select id="company_id" class="form-control category_id form-control-custom-select" name="company_id">
                                    <option value="">Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" {{ $company->id == $defaultCompany ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="company" class="form-label">Company<span class="text-danger"> *</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="category_id" id="category_id" class="form-control category_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Category <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="sub_category_id" id="sub_category_id" class="form-control sub_category_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Sub Category <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="number" id="amount" name="amount" placeholder="">
                                <label for="amount">Amount<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="date" id="expense_date" name="expense_date" placeholder="">
                                <label for="expense_date">Date</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="date" id="due_date" name="due_date" placeholder="">
                                <label for="due_date">Due Date<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="vendor_id" id="vendor_id" class="form-control vendor_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Vendor <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="vendor_account_id" id="vendor_account_id" class="form-control vendor_account_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Vendor Account</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="description" style="height: 100px" name="description"></textarea>
                                <label for="floatingTextarea">Description <span class="text-danger"> *</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <div class="custom-file-upload">
                                <label for="pdf" class="custom-file-label">
                                    <i class="fas fa-upload"></i> Upload PDF
                                </label>
                                <input type="file" id="pdf" name="pdf" accept="application/pdf" class="form-control-file" onchange="previewPDF(event)">
                            </div>
                            <div id="pdf-preview" class="mt-3 hidden">
                                <span id="pdf-filename"></span>
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
{{-- <div class="modal fade" id="viewModel" tabindex="-1" role="dialog" aria-labelledby="viewModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModelLabel"> <span id="model-main-title_view"></span> Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm view_data" id="pc-dt-simple">
                            <tbody>
                                <tr>
                                    <th>Category</th>
                                    <td>:</td>
                                    <td id="view_category"></td>
                                    <th>Sub Category</th>
                                    <td>:</td>
                                    <td id="view_sub_category"></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>:</td>
                                    <td id="view_description"></td>
                                   <th>Expense Date</th>
                                    <td>:</td>
                                    <td id="view_expens_date"></td>
                                </tr>
                                <tr>
                                    <th>Finance Status</th>
                                    <td>:</td>
                                    <td id="view_finance_status"></td>
                                    <th>Payment Status</th>
                                    <td>:</td>
                                    <td id="view_payment_status"></td>
                                </tr>
                                <tr>
                                   <th>Expense Amount</th>
                                    <td>:</td>
                                    <td id="view_expens_amount"></td>
                                    <th>Expense Balance</th>
                                    <td>:</td>
                                    <td id="view_balance"></td>
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
                                    <th>Vendor Name</th>
                                    <td>:</td>
                                    <td><a id="view_vendor_name"></a></td>
                                    <th>Vendor Account</th>
                                    <td>:</td>
                                    <th id="view_vendor_account">-</th>
                                </tr>
                                <tr>
                                    <th>Attachment (PDF)</th>
                                    <td>:</td>
                                    <td><a id="view_attachment">No attachment available</a></td>
                                    <th>Code</th>
                                    <td>:</td>
                                    <th id="view_code"></th>
                                </tr>
                                <tr>
                                    <th>Payment Due Date</th>
                                    <td>:</td>
                                    <td><span id="view_due_date"></span></td>
                                    <th></th>
                                    <td>:</td>
                                    <th id=""></th>
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
</div> --}}
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Expenses</h4>
            </div>
            <div class="card-body">
                @can('finance-expenses-module-create')
                <button type="button" class="btn btn-primary btn-sm btn-add-new" data-bs-toggle="modal" data-bs-target="#createModel" data-bs-whatever="@mdo"><i class="ph-duotone ph-plus-circle"></i> Create Expense</i></button>
                @endcan
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Code</th>
                                <th>Company</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Vendor</th>
                                <th>Expense Date</th>
                                <th>Finance Status</th>
                                <th>Payment Status</th>
                                <th>Created By</th>
                                <th>Amount</th>
                                <th>Balance</th>
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
            , ajax: "{{ route('finance.expenses.index')}}"
            , columns: [{
                    data: 'id'
                    , name: 'id'
                },{
                    data: 'code'
                    , name: 'code'
                },
                {
                    data: 'company'
                    , name: 'company'
                }
                , {
                    data: 'category.name'
                    , name: 'category.name'
                }
                , {
                    data: 'sub_category.name'
                    , name: 'sub_category.name'
                }, {
                    data: 'vendor.name'
                    , name: 'vendor.name'
                }
                , {
                    data: 'expense_date'
                    , name: 'expense_date'
                    , render: function(data) {
                        // Convert the data to a Date object and format it
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US'); // You can customize the locale
                    }
                }
                , {
                    data: 'status'
                    , name: 'status'
                }
                , {
                    data: 'paymnet_status'
                    , name: 'paymnet_status'
                }
                , {
                    data: 'created_by.name'
                    , name: 'created_by.name'
                }
                , {
                    data: 'amount'
                    , name: 'amount'
                }
                , {
                    data: 'balance'
                    , name: 'balance'
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
            url: '/admin/getVendors'
            , method: 'GET'
            , success: function(response) {
                $('#vendor_id').empty();
                $("#vendor_id").append($("<option />"));
                $.each(response, function(key, option) {
                    $("#vendor_id").append($("<option />")
                        .val(option.id)
                        .text(option.name));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
        $.ajax({
            url: '/admin/getBanks'
            , method: 'GET'
            , success: function(response) {
                $('#bank_id').empty();
                $("#bank_id").append($("<option />"));
                $.each(response, function(key, option) {
                    $("#bank_id").append($("<option />")
                        .val(option.id)
                        .text(option.bank_name + ' - ' + option.bank_code));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).ready(function() {
        $('#sub_category_id').empty();
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
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('change', '.category_id', function() {
        let id = $(this).val();
        $('#sub_category_id').empty();
        $.ajax({
            url: '/finance/get_sub_category/' + id
            , method: 'GET'
            , success: function(response) {
                $('#sub_category_id').empty();
                $("#sub_category_id ").append($("<option />"));
                $.each(response.sub_categories, function(key, option) {
                    $("#sub_category_id").append($("<option />")
                        .val(option.id)
                        .text(option.name));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('change', '.vendor_id', function() {
        let id = $(this).val();
        $('#vendor_account_id').empty();
        $.ajax({
            url: '/admin/get_vendor_accounts/' + id
            , method: 'GET'
            , success: function(response) {
                $('#vendor_account_id').empty();
                $("#vendor_account_id ").append($("<option />"));
                $.each(response.accounts, function(key, option) {
                    $("#vendor_account_id").append($("<option />")
                        .val(option.id)
                        .text(option.account_number+" -- "+ option.mobile));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
        $('#submitForm input[name="_method"][value="put"]').remove();
        $('#submitForm').attr('action', "{{ route('finance.expenses.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/finance/expenses/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#category_id').val(response.expense.category_id).trigger('change');
                setTimeout(function() {
                    $('#sub_category_id').val(response.expense.sub_category_id).trigger('change');
                }, 500);
                $('#amount').val(response.expense.amount);
                $('#expense_date').val(response.expense.expense_date);
                $('#description').val(response.expense.description);
                $('#submitForm').append('<input type="hidden" name="old_pdf" id="old_pdf">');
                $('#old_pdf').val(response.expense.pdf);
                $('#due_date').val(response.expense.due_date);
                $('#vendor_id').val(response.expense.vendor_id).trigger('change')
                setTimeout(function() {
                    $('#vendor_account_id').val(response.expense.vendor_account_id).trigger('change');
                }, 700);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('click', '.btnView', function() {
        var id = $(this).data('id');
        $('#submitForm input[name="_method"][value="put"]').remove();
        $('#submitForm').attr('action', "{{ route('finance.expenses.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/finance/expenses/' + id
            , method: 'GET'
            , success: function(response) {
                $('#view_data').empty();
                $('#view_category').text(response.expense.category.name);
                $('#view_sub_category').text(response.expense.sub_category.name);
                $('#view_description').text(response.expense.description);
                $('#view_expens_date').text(response.expense.expense_date);
                $('#view_expens_amount').text(response.expense.amount);
                $('#view_balance').text(response.expense.balance);
                $('#view_approved_user').text(response.expense.approved_by ? response.expense.approved_by.name : '-');
                $('#view_approval_comment').text(response.expense.approved_comment?response.expense.approved_comment:'-');
                $('#view_approved_date').text(response.expense.approved_date ? new Date(response.expense.approved_date).toLocaleDateString('en-GB') : '-');
                $('#view_reject_user').text(response.expense.rejected_by ? response.expense.rejected_by.name : '-');
                $('#view_reject_comment').text(response.expense.rejected_comment ? response.expense.rejected_comment : '-');
                $('#view_rejected_date').text(response.expense.rejected_date ? new Date(response.expense.rejected_date).toLocaleDateString('en-GB') : '-');
                $('#view_created_by').text(response.expense.created_by.name);
                $('#view_created_at').text(new Date(response.expense.created_at).toLocaleDateString('en-GB'));
                $('#view_deleted_by').text(response.expense.deleted_by ? response.expense.deleted_by.name : '-');
                $('#view_deleted_at').text(response.expense.deleted_at ? new Date(response.expense.deleted_at).toLocaleDateString('en-GB') : '-');
                $('#view_code').text(response.expense.code);
               $('#view_due_date').text(new Date(response.expense.due_date).toLocaleDateString('en-GB'));
                $('#view_vendor_name').text(response.expense.vendor.name);
                $('#view_vendor_account').text(response.expense.vendor_account?response.expense.vendor_account.account_number + " / " +response.expense.vendor_account.mobile:"-");
                if (response.expense.pdf) {
                    $('#view_attachment')
                        .text(response.expense.pdf.split('/').pop())
                        .attr('href','/storage/'+response.expense.pdf)
                        .attr('target', '_blank')
                        .attr('rel', 'noopener noreferrer');
                } else {
                    $('#view_attachment')
                        .text('No attachment available')
                        .removeAttr('href')
                        .removeAttr('target')
                        .removeAttr('rel');
                }
                const status = response.expense.status;
                let capitalizedStatus="";
                let badgeClass;
                if (status === 'pending') {
                    capitalizedStatus = "Pending";
                    badgeClass = 'badge bg-warning';
                } else if (status === 'approved') {
                 capitalizedStatus = "Approved";
                    badgeClass = 'badge bg-primary';
                } else if (status === 'paid') {
                 capitalizedStatus = "Paid";
                    badgeClass = 'badge bg-success-1';
                } else if(status === 'rejected') {
                    capitalizedStatus = "Rejected";
                    badgeClass = 'badge bg-danger';
                }
                const paymnet_status = response.expense.paymnet_status;
                let capitalizedPaymnet_status="";
                let badgeClass_2;
                if (paymnet_status === 'pending') {
                    capitalizedPaymnet_status = "Pending";
                    badgeClass_2 = 'badge bg-warning';
                } else if (paymnet_status === 'partially') {
                 capitalizedPaymnet_status = "Partially";
                    badgeClass_2 = 'badge bg-primary';
                } else if (paymnet_status === 'complete') {
                 capitalizedPaymnet_status = "Complete";
                    badgeClass_2 = 'badge bg-success-1';
                }
                $('#view_finance_status').html(`<span class="${badgeClass}" style="">${capitalizedStatus}</span>`);
                $('#view_payment_status').html(`<span class="${badgeClass_2}" style="">${capitalizedPaymnet_status}</span>`);

            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

</script>
<script>
    function previewPDF(event) {
        const file = event.target.files[0];
        if (!file || file.type !== "application/pdf") {
            alert("Please upload a valid PDF file.");
            return;
        }

        const pdfPreview = document.getElementById("pdf-preview");
        const pdfFilename = document.getElementById("pdf-filename");
        pdfFilename.textContent = file.name;

        pdfPreview.classList.remove("hidden");
    }

</script>
@endsection
