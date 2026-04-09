@extends('layouts.main')

@section('title', 'Expenses')
@section('breadcrumb-item', 'Finance')
@section('breadcrumb-item-active', 'Payments')
@section('content')
<style>
    .card-body-1 {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f8f8;
    }

    .cheque {
        width: 1000px;
        height: 450px;
        border: 1px solid #000;
        margin: 50px auto;
        padding: 20px;
        position: relative;
        background-image: url('/path_to_image/HattonNationalBank_LK.jpg');
        /* Replace with correct path */
        background-size: cover;
        background-repeat: no-repeat;
    }

    /* Bank logo area */
    .bank-logo {
        position: absolute;
        top: 20px;
        left: 30px;
    }

    /* Date field */
    .date {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 16px;
        letter-spacing: 5px;
    }

    /* Payee field */
    .payee {
        position: absolute;
        top: 120px;
        left: 30px;
        font-size: 15px;
        font-weight: bold;
    }

    /* Amount in words */
    .amount-words {
        position: absolute;
        top: 200px;
        left: 30px;
        width: 600px;
        font-size: 15px;
        font-weight: bold;
    }

    /* Amount in figures */
    .amount-figures {
        position: absolute;
        top: 200px;
        right: 30px;
        font-size: 20px;
        font-weight: bold;
    }

    .layout_payee {
        position: absolute;
        top: 300px;
        /* Adjust the vertical position as needed */
        left: 30px;
        /* Adjust the horizontal position as needed */
        text-align: left;
        /* Aligns content to the left */
        font-size: 15px;
        font-weight: bold;
        width: auto;
        /* Allows the text to determine the width */
    }

    .layout_payee::before,
    .layout_payee::after {
        content: '';
        display: block;
        width: 100%;
        /* Matches the text width */
        height: 1px;
        /* Thickness of the line */
        background-color: black;
        /* Line color */
        margin: 0;
        /* Removes extra space around the lines */
    }

    .layout_payee::before {
        margin-bottom: 2px;
        /* Space between the top line and the text */
    }

    .layout_payee::after {
        margin-top: 2px;
        /* Space between the bottom line and the text */
    }


    .layout_payee_validity {
        position: absolute;
        top: 350px;
        /* Adjust the vertical position as needed */
        left: 30px;
        /* Adjust the horizontal position as needed */
        text-align: left;
        /* Aligns content to the left */
        font-size: 15px;
        font-weight: bold;
        width: auto;
        /* Allows the text to determine the width */
    }

    .layout_payee_validity::before,
    .layout_payee_validity::after {
        content: '';
        display: block;
        width: 100%;
        /* Matches the text width */
        height: 1px;
        /* Thickness of the line */
        background-color: black;
        /* Line color */
        margin: 0;
        /* Removes extra space around the lines */
    }

    .layout_payee_validity::before {
        margin-bottom: 2px;
        /* Space between the top line and the text */
    }

    .layout_payee_validity::after {
        margin-top: 2px;
        /* Space between the bottom line and the text */
    }

    /* Signature area */

    .signature-section {
        position: absolute;
        bottom: 40px;
        right: 30px;
        font-size: 16px;
        text-align: center;
    }

    .signature {
        margin-bottom: 10px;
    }

    /* Footer info (Cheque number, branch code, etc.) */
    .footer-info {
        position: absolute;
        bottom: 10px;
        left: 30px;
        font-size: 12px;
    }

    /* Additional design details */
    .amount-label {
        position: absolute;
        top: 170px;
        left: 30px;
        font-size: 14px;
    }

    .amount-box {
        position: absolute;
        top: 205px;
        right: 200px;
        font-size: 14px;
    }

    .center {
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .layout_company_logo {
    position: absolute;
    top: 300px;
    left: 750px;
}
 /* Add more specific styling as necessary */
</style>
<!-- [ Main Content ] start -->
<div class="modal fade" id="viewModel" tabindex="-1" role="dialog" aria-labelledby="viewModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModelLabel">View Expense</h5>
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
                                    <th>Paid Amount</th>
                                    <td>:</td>
                                    <th id="view_paid_amount"></th>
                                    <th>Payment Due Date</th>
                                    <td>:</td>
                                    <td id="view_due_date"></td>
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
<div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModelLabel">Create Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm payment_data" id="pc-dt-simple">
                        <tbody>
                            <tr>
                                <th>Expense Code</th>
                                <td>:</td>
                                <td id="payment_expense_code"></td>
                                <th>Category</th>
                                <td>:</td>
                                <td id="payment_category"></td>
                            </tr>
                            <tr>
                                <th>Sub Category</th>
                                <td>:</td>
                                <td id="payment_sub_category"></td>
                                <th>Expense Amount</th>
                                <td>:</td>
                                <td id="payment_expense_amount"></td>
                            </tr>
                            <tr>
                                <th>Vendor Name</th>
                                <td>:</td>
                                <td><a id="payment_view_vendor_name"></a></td>
                                <th>Vendor Account</th>
                                <td>:</td>
                                <th id="payment_view_vendor_account">-</th>
                            </tr>
                            <tr>
                                <th>Expense Amount Balance</th>
                                <td>:</td>
                                <td id="payment_expense_amount_balance"></td>
                                <th></th>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <form id="submitForm" method="POST" action="{{route('finance.expense.payment.store')}}">
                    @csrf
                    <input type="hidden" id="expense_id" name="expense_id">
                    <input type="hidden" id="total_expense_amount" name="expense_amount">
                    <input type="hidden" id="total_payment_expense_amount_balance">
                    <div class="form-group row g-4">
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
                                <select name="bank_account_id" id="bank_account_id" class="form-control bank_account_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Bank Account <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="cheque_book_id" id="cheque_book_id" class="form-control cheque_book_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name">Cheque Book<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="cheque_book_detail_id" id="cheque_book_detail_id" class="form-control cheque_book_detail_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name">Cheque Number<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="number" id="paid_amount" name="amount" placeholder="">
                                <label for="amount">Paid Amount<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="date" id="cheque_date" name="cheque_date" placeholder="" min="{{now()}}">
                                <label for="cheque_date">Cheque Date<span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <select name="signatory_id" id="signatory_id" class="form-control signatory_id form-control-custom-select">
                                    <option value=""></option>
                                </select>
                                <label for="name"> Authorized Signatory <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-floating mb-0">
                                <input class="mb-0 form-control form-control-custom" type="text" id="referance_no" name="referance_no" placeholder="">
                                <label for="name">Bill / Invoice No</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check-label mb-2" for="">Payee Name </label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='payee_name' value="0" id="only_name" checked>
                                <label class="form-check-label" for="only_name">Only Name</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='payee_name' value="1" id="name_with_nic">
                                <label class="form-check-label" for="name_with_nic">Name with NIC</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check-label mb-2" for="">Payment Condition </label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='payment_condition' value="0" id="cash_payee" checked>
                                <label class="form-check-label" for="cash_payee"> Cash Payment</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='payment_condition' value="1" id="account_payee">
                                <label class="form-check-label" for="account_payee">Account Payee</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='payment_condition' value="2" id="not_negotiable">
                                <label class="form-check-label" for="Not Negotiable"> Account Payee - Not Negotiable</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check-label mb-2" for="">Validity Period</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='validity_period' value="0" id="validity_none" checked>
                                <label class="form-check-label" for="none">None</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='validity_period' value="1" id="validity_30_days">
                                <label class="form-check-label" for="30_days">30 Days</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name='validity_period' value="2" id="validity_60_days">
                                <label class="form-check-label" for="60_days">60 Days</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                        </div>
                        <hr>
                        <div class="card-body-1" id="cheque_layout" style="display:none">
                            <div class="cheque">
                                <!-- Bank Logo -->
                                <div class="bank-logo"><span style="font-size: 17px" id="layout_bank_name"></span></div>
                                <!-- Date -->
                                <div class="date"><span id="layout_cheque_date"></span></div>

                                <!-- Payee Name -->
                                <div class="payee"><span id="layout_vendor_name"></span></div>

                                <!-- Amount in Words -->
                                <div class="amount-label">Rupees:</div>
                                <div class="amount-words">**<span id="layout_amount_on_words"></span>**</div>

                                <!-- Amount in Figures -->
                                <div class="amount-box">Rs.</div>
                                <div class="amount-figures">**<span id="layout_payment_amount"></span>**</div>
                                <div class="layout_company_logo">
                                    @php
                                        use App\Models\Company;
                                        $company = Company::first(); // Fetch the first company record
                                    @endphp
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" width="25%">
                                </div>
                                <div class="layout_payee">
                                    <span id="layout_payee"></span>
                                </div>
                                <div class="layout_payee_validity">
                                    <span id="layout_payee_validity"></span>
                                </div>

                                <!-- Signature Placeholder -->
                                <div class="signature-section">
                                    <!-- Option 1: One Director -->
                                    <div class="signature" id="1" style="display: none;">
                                        ___________________<br>
                                        Director
                                    </div>

                                    <!-- Option 2: Two Directors -->
                                    <div class="signature" id="2" style="display: none;">
                                        ___________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___________________<br>
                                        Director &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Director
                                    </div>

                                    <!-- Option 3: Three Directors -->
                                    <div class="signature" id="3" style="display: none;">
                                        ___________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___________________<br>
                                        Director &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Director &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Director
                                    </div>

                                    <!-- Option 4: One Director + Authorized Signatory -->
                                    <div class="signature" id="4" style="display: none;">
                                        ___________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___________________<br>
                                        Director &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Authorized Signatory
                                    </div>

                                    <!-- Option 5: Two Directors + Authorized Signatory -->
                                    <div class="signature" id="5" style="display: none;">
                                        ___________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ___________________<br>
                                        Director &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Director &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Authorized Signatory
                                    </div>
                                </div>
                                <!-- Footer Info: Cheque Number, Branch Code, etc. -->
                                <div class="footer-info">
                                    PLEASE DO NOT WRITE BELOW THIS LINE | CHEQUE NO: <span id="layout_cheque_no"></span> | BANK CODE: <span id="layout_cheque_bank_code"></span> / BRANCH CODE: <span id="layout_cheque_branch_code"></span>
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
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>To Be Paid Expenses</h4>
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
                                <th>Vendor</th>
                                <th>Expense Date</th>
                                <th>Finance Status</th>
                                <th>Payment Status</th>
                                <th>Approved By</th>
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
            // , ajax: "{{ route('finance.expense.payment.index')}}"
            ,ajax: {
                    url: "{{ route('finance.expense.payment.index')}}",
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
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }, {
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
                    data: 'approved_by.name'
                    , name: 'approved_by.name'
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
    $.ajax({
        url: '/admin/authorized_signatories'
        , method: 'GET'
        , success: function(response) {
            $('#signatory_id').empty();
            $("#signatory_id ").append($("<option />"));
            $.each(response, function(key, signatory) {
                $("#signatory_id").append($("<option />")
                    .val(signatory.id)
                    .text(signatory.name));
            });
        }
        , error: function(xhr) {
            console.error('An error occurred:', xhr.responseText);
        }
    });
    $(document).on('click', '.btnView', function() {
        var id = $(this).data('id');
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
                $('#view_approval_comment').text(response.expense.approved_comment ? response.expense.approved_comment : '-');
                $('#view_approved_date').text(response.expense.approved_date ? new Date(response.expense.approved_date).toLocaleDateString('en-GB') : '-');
                $('#view_reject_user').text(response.expense.rejected_by ? response.expense.rejected_by : '-');
                $('#view_reject_comment').text(response.expense.rejected_comment ? response.expense.rejected_comment : '-');
                $('#view_rejected_date').text(response.expense.rejected_date ? new Date(response.expense.rejected_date).toLocaleDateString('en-GB') : '-');
                $('#view_created_by').text(response.expense.created_by.name);
                $('#view_created_at').text(new Date(response.expense.created_at).toLocaleDateString('en-GB'));
                $('#view_deleted_by').text(response.expense.deleted_by ? response.expense.deleted_by.name : '-');
                $('#view_deleted_at').text(response.expense.deleted_at ? new Date(response.expense.deleted_at).toLocaleDateString('en-GB') : '-');
                $('#view_code').text(response.expense.code);
                $('#view_vendor_name').text(response.expense.vendor.name);
                $('#view_vendor_account').text(response.expense.vendor_account ? response.expense.vendor_account.account_number + " / " + response.expense.vendor_account.mobile : "-");
               $('#view_due_date').text(new Date(response.expense.due_date).toLocaleDateString('en-GB'));
                if (response.expense.pdf) {
                    $('#view_attachment')
                        .text(response.expense.pdf.split('/').pop())
                        .attr('href', '/storage/' + response.expense.pdf)
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
                let capitalizedStatus = "";
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
                } else if (status === 'rejected') {
                    capitalizedStatus = "Rejected";
                    badgeClass = 'badge bg-danger';
                }
                const paymnet_status = response.expense.paymnet_status;
                let capitalizedPaymnet_status = "";
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

    $(document).on('click', '.btnPayment', function() {
        $("#submitForm input").attr("disabled", false);
        $("#submitForm textarea").attr("disabled", false);
        var id = $(this).data('id');
        $('#submitForm').attr('action', "{{ route('finance.expense.payment.store') }}");
        $.ajax({
            url: '/finance/expenses/' + id
            , method: 'GET'
            , success: function(response) {
                $('.modal-footer').show();
                $('#payment_data').empty();
                $('#expense_id').val(id);
                $('#payment_expense_code').text(response.expense.code);
                $('#payment_category').text(response.expense.category.name);
                $('#payment_sub_category').text(response.expense.sub_category.name);
                $('#payment_expense_amount').text(response.expense.amount);
                $('#payment_expense_amount_balance').text(response.expense.balance);
                $('#total_expense_amount').val(response.expense.amount);
                $('#total_payment_expense_amount_balance').val(response.expense.balance);
                $('#payment_view_vendor_name').text(response.expense.vendor.name);
                $('#layout_vendor_name').text(response.expense.vendor.name);
                $('#payment_view_vendor_account').text(response.expense.vendor_account ? response.expense.vendor_account.account_number + " / " + response.expense.vendor_account.mobile : "-");
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

    $(document).on('change', '.bank_id', function() {
        $('#cheque_layout').show();
        $('#bank_account_id').empty();
        $('#cheque_book_id').empty();
        // $('#vendor_id').empty();
        $('#paid_amount').empty();
        $('#cheque_date').empty();
        let id = $(this).val();
        $.ajax({
            url: '/admin/getAccounts/' + id
            , method: 'GET'
            , success: function(response) {
                $('#layout_bank_name').text(response[0].bank.bank_name);
                $('#bank_account_id').empty();
                $('#layout_cheque_bank_code').text(response[0].bank.bank_code);
                $('#layout_cheque_branch_code').text(response[0].branch.bank_branch_code);
                $("#bank_account_id").append($("<option />"));
                $.each(response, function(key, option) {
                    $("#bank_account_id").append($("<option />")
                        .val(option.id)
                        .text(option.account_name + ' - ' + option.account_no));
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
            url: '/admin/getChequeBooks/' + id
            , method: 'GET'
            , success: function(response) {
                // $('#layout_cheque_no').text()
                $('#cheque_book_id').empty();
                $("#cheque_book_id").append($("<option />"));
                $.each(response, function(key, option) {
                    $("#cheque_book_id").append($("<option />")
                        .val(option.id)
                        .text(option.bank_account.account_name + " / " + option.nikname));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

    $(document).on('change', '#cheque_date', function() {
        let cheque_date = $(this).val();
        $('#layout_cheque_date').text(cheque_date ? new Date(cheque_date).toLocaleDateString('en-GB') : 'dd/MM/yyyy');
    });


    $(document).on('change', '#only_name', function() {
        let expense_id = $('#expense_id').val();
        api_call_back(expense_id, function(data) {
            $('#layout_vendor_name').text(data.expense.vendor.name);
        });
    });

    $(document).on('change', '#name_with_nic', function() {
        let expense_id = $('#expense_id').val();
        api_call_back(expense_id, function(data) {
            if (data.expense.vendor.nic) {
                $('#layout_vendor_name').text(data.expense.vendor.name + " / " + data.expense.vendor.nic);
            } else {
                $('#layout_vendor_name').text(data.expense.vendor.name);
            }
        });

    });

    function api_call_back(expense_id, callback) {
        $.ajax({
            url: '/finance/expenses/' + expense_id
            , method: 'GET'
            , success: function(response) {
                callback(response); // Call the callback with the response
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    }

    $(document).on('change', '#cash_payee', function() {
        $('#layout_payee').text(' ');
    });
    $(document).on('change', '#account_payee', function() {
        $('#layout_payee').text('A/C PAYEE ONLY');
    });
    $(document).on('change', '#not_negotiable', function() {
        $('#layout_payee').text('A/C PAYEE ONLY - NOT NEGOTIABLE');
    });

    $(document).on('change', '#validity_none', function() {
        $('#layout_payee_validity').text('');
    });
    $(document).on('change', '#validity_30_days', function() {
        $('#layout_payee_validity').text('VALID ONLY FOR 30 DAYS');
    });
    $(document).on('change', '#validity_60_days', function() {
        $('#layout_payee_validity').text('VALID ONLY FOR 60 DAYS');
    });
    $(document).on('change', '#paid_amount', function() {
        let layout_payment_amount = parseFloat($(this).val()) || 0;
        let paid_total_expense_amount = parseFloat($('#total_payment_expense_amount_balance').val()) || 0;

        if (paid_total_expense_amount >= layout_payment_amount && layout_payment_amount > 0) {
            $('#layout_payment_amount').text(layout_payment_amount);

            $.ajax({
                url: '/finance/expense/payment/amountOnWords/'
                , method: 'GET'
                , dataType: 'json'
                , data: {
                    amount: layout_payment_amount
                }
                , success: function(response) {
                    if (response.amount_on_words) {
                        $('#layout_amount_on_words').text(response.amount_on_words);
                    } else {
                        $('#layout_amount_on_words').text("Error in response");
                    }
                }
                , error: function(xhr) {
                    Swal.fire({
                        icon: "error"
                        , title: "Error"
                        , text: "Could not fetch amount in words. Please try again."
                    });
                }
            });
        } else {
            Swal.fire({
                icon: "error"
                , title: "Oops..."
                , text: "Paid amount cannot be greater than the expense balance."
            });

            $('#paid_amount').val('');
            $('#layout_payment_amount').text("");
            $("#layout_amount_on_words").text("");
        }
    });

    $(document).on('change', '.cheque_book_id', function() {
        let id = $(this).val();
        $.ajax({
            url: '/admin/getCheque/' + id
            , method: 'GET'
            , success: function(response) {
                $('#cheque_book_detail_id').empty();
                $("#cheque_book_detail_id").append($("<option />"));
                $.each(response, function(key, option) {
                    $("#cheque_book_detail_id").append($("<option />")
                        .val(option.id)
                        .text(option.cheque_number));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });
    $(document).on('change', '.cheque_book_detail_id', function() {
        let val = $('#cheque_book_detail_id option:selected').text();;
        $('#layout_cheque_no').text(val);
    });

    $(document).on('change', '.signatory_id', function() {
        let val = $(this).val();
        toggleSignatureLayout(val);
    });

    function toggleSignatureLayout(option) {
        // Hide all signature layouts
        document.querySelectorAll('.signature').forEach(el => el.style.display = 'none');
        // Show the selected signature layout
        const selectedSignature = document.getElementById(option);
        if (selectedSignature) {
            selectedSignature.style.display = 'block';
        }
    }

    // Example: To display the two-director signature layout
    document.getElementById('paid_amount').addEventListener('input', function (event) {
    const value = event.target.value;
    if (value && !/^\d*(\.\d{0,2})?$/.test(value)) {
        event.target.value = value.slice(0, -1); // Remove invalid character
        }
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
