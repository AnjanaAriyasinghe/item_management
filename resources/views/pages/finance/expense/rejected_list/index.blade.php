@extends('layouts.main')

@section('title', 'Expenses')
@section('breadcrumb-item', 'Finance')
@section('breadcrumb-item-active', 'Approval Rejected List')
@section('content')
<!-- [ Main Content ] start -->
<div class="modal fade" id="viewModel" tabindex="-1" role="dialog" aria-labelledby="viewModelLabel" aria-hidden="true">
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
                                    <td></td>
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
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Rejected List</h4>
            </div>
            <div class="card-body">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-4 ">
                                <div class="form-group form-floating mb-0">
                                    <select name="search_company_id" id="search_company_id" class="form-control search_company_id form-control-custom-select">
                                        <option value=""></option>
                                        <option value="all">All</option>
                                        @foreach ($companies as $company)
                                            {{-- <option value="{{ $company->id }}">{{ $company->name }}</option> --}}
                                            <option value="{{ $company->id }}" {{ $company->id == $defaultCompany ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
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
                                <th>Code</th>
                                <th>Company</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>vendor</th>
                               <th>Expense Date</th>
                                <th>Finance Status</th>
                                <th>Paymnet Status</th>
                                <th>Rejected By</th>
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
            // , ajax: "{{ route('finance.expense.approval.rejected_list')}}"
            ,ajax: {
                    url: "{{ route('finance.expense.approval.rejected_list')}}",
                    data: function(d) {

                        d.company_id = $('#search_company_id').val();
                         console.log('data company id:',d.company_id)
                    }
                },
            drawCallback: function() {
                    // Hide spinner and show search button after successful response
                    $('.spinner-border').hide();
                    $('#search').show();
                }
            ,columns: [{
                    data: 'id'
                    , name: 'id'
                },{
                    data: 'code'
                    , name: 'code'
                },{
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
                    data: 'rejected_by.name'
                    , name: 'rejected_by.name'
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
         $("#search").on("click", function() {
            table.draw();
        });
    });
    $(document).ready(function() {
        $.ajax({
            url: '/admin/expense_sub_categories/getcategory'
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
                $('#view_vendor_name').text(response.expense.vendor.name);
                $('#view_vendor_account').text(response.expense.vendor_account?response.expense.vendor_account.account_number + " / " +response.expense.vendor_account.mobile:"-");
               $('#view_due_date').text(new Date(response.expense.due_date).toLocaleDateString('en-GB'));
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
