@extends('layouts.main')

@section('title', 'Cheque Books')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Clear Cheque')
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
<div class="modal fade" id="approvalModel" tabindex="-1" role="dialog" aria-labelledby="approvalModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Cleared</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitFormBtnApproval" method="POST" action="{{route('admin.cheque.cleared')}}">
                    @csrf
                    <input type="hidden" name="id" id="data_value">
                    <div class="form-group row g-4">
                        <div class="col-md-12">
                            <div class="form-group form-floating">
                                <textarea class="form-control" id="cleared_comment" style="height: 100px" name="cleared_comment"></textarea>
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

<div class="modal fade" id="viewModel" tabindex="-1" role="dialog" aria-labelledby="viewModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModelLabel"> <span id="model-main-title_view"></span> Cheque Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="center">
                        <h4>This Cheque Is <span id="cheque_status">Availabale</span></h4>
                    </div>
                    <div class="card-body-1">
                        {{-- <div class="cheque">
                            <!-- Bank Logo -->
                            <div class="bank-logo"><span style="font-size: 17px" id="layout_bank_name"></span></div>
                            <!-- Date -->
                            <div class="date" ><span id="layout_cheque_date"></span></div>

                             <!-- Payee Name -->
                             <div class="payee"><span id="layout_vendor_name"></span></div>

                             <!-- Amount in Words -->
                             <div class="amount-label">Rupees:</div>
                             <div class="amount-words">**<span id="layout_amount_on_words"></span> Only**</div>

                             <!-- Amount in Figures -->
                             <div class="amount-box">Rs.</div>
                             <div class="amount-figures">**<span id="layout_payment_amount"></span>**</div>

                            <!-- Signature Placeholder -->
                            <div class="signature">
                                ___________________<br>
                                Authorized Signature
                            </div>

                            <!-- Footer Info: Cheque Number, Branch Code, etc. -->
                            <div class="footer-info">
                                PLEASE DO NOT WRITE BELOW THIS LINE | CHEQUE NO: <span id="layout_cheque_no"></span> | BANK / BRANCH CODE: <span id="layout_cheque_bank_code"></span><span id="layout_cheque_branch_code"></span>
                            </div>
                        </div> --}}
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
                    <div class="table-responsive">
                        <table class="table table-sm view_data" id="pc-dt-simple">
                            <tbody>
                                <tr>
                                    <th>Cheque Date</th>
                                    <td>:</td>
                                    <td id="view_cheque_date"></td>
                                    <th>Cheque Book Code</th>
                                    <td>:</td>
                                    <td id="view_book_code"></td>
                                </tr>
                                <tr>
                                    <th>Account Name</th>
                                    <td>:</td>
                                    <td id="view_account_name"></td>
                                    <th>Account Number</th>
                                    <td>:</td>
                                    <td id="view_account_number"></td>
                                </tr>
                                <tr>
                                    <th>Bank Name</th>
                                    <td>:</td>
                                    <td id="view_bank_name"></td>
                                    <th>Branch Name</th>
                                    <td>:</td>
                                    <td id="view_branch_name"></td>
                                </tr>
                                <tr>
                                    <th>Vendor Name</th>
                                    <td>:</td>
                                    <td id="view_vendor_name"></td>
                                    <th>Vendor Account</th>
                                    <td>:</td>
                                    <td id="view_vendor_account"></td>
                                </tr>
                                <tr>
                                    <th>Payment Category</th>
                                    <td>:</td>
                                    <td id="view_payment_category"></td>
                                    <th>Payment Sub Category</th>
                                    <td>:</td>
                                    <td id="view_payment_sub_category"></td>
                                </tr>
                                <tr>
                                    <th>Issue Date</th>
                                    <td>:</td>
                                    <td id="view_isssued_date"></td>
                                    <th>Issued By</th>
                                    <td>:</td>
                                    <td id="view_issued_by"></td>
                                </tr>
                                <tr>
                                    <th>Clear Date</th>
                                    <td>:</td>
                                    <td id="view_cleared_date"></td>
                                    <th>Cleared By</th>
                                    <td>:</td>
                                    <td id="view_cleared_by"></td>
                                </tr>
                                <tr>
                                    <th>Cancel Date</th>
                                    <td>:</td>
                                    <td id="view_canceled_date"></td>
                                    <th>Cancelled By</th>
                                    <td>:</td>
                                    <td id="view_cancelled_by"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>:</td>
                                    <td id="view_status"></td>
                                    <th>Amount</th>
                                    <td>:</td>
                                    <th id="view_payment_amount"></th>
                                </tr>
                                <tr>
                                    <th>Nikname</th>
                                    <td>:</td>
                                    <td id="view_nikname"></td>
                                    <th>Cheque Number</th>
                                    <td>:</td>
                                    <td id="view_cheque_no"></td>
                                </tr>
                                <tr>
                                    <th>Bill / Invoice</th>
                                    <td>:</td>
                                    <td id="view_invoice_no"></td>
                                    <th></th>
                                    <td>:</td>
                                    <td id=""></td>
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
                <h4>Cheques</h4>
            </div>
            <div class="card-body">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-4 ">
                                <div class="form-group form-floating mb-0">
                                    <select name="cheque_book_id" id="cheque_book_id" class="form-control cheque_book_id form-control-custom-select">
                                        <option value=""></option>
                                        <option value="all">All</option>
                                    </select>
                                    <label for="name">Cheque Books<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group form-floating mb-0">
                                    <select name="status" id="status" class="form-control status form-control-custom-select">
                                        <option value=""></option>
                                        <option value="all">All</option>
                                        <option value="pending">Available</option>
                                        <option value="issued">Issued</option>
                                        <option value="cleared">Cleared</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    <label for="name">Status<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-info" id="search">Search</button>
                            </div>
                        </div>
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Ac Name</th>
                                <th>Book Code</th>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Cheque No</th>
                                <th>Bill / Invoice No</th>
                                <th>Status</th>
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
            url: '/admin/getChequeBooksAll'
            , method: 'GET'
            , success: function(response) {
                $('#cheque_book_id').empty();
                $("#cheque_book_id ").append($("<option />"));
                $("#cheque_book_id").append($("<option />").val("all").text("All"));
                $.each(response, function(key, option) {
                    $("#cheque_book_id").append($("<option />")
                        .val(option.id)
                        .text(option.bank_account.account_name +" / " +option.nikname));
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
                url: "{{ route('admin.cheque_books.cheques') }}"
                , data: function(d) {
                    d.cheque_book_id = $('#cheque_book_id').val();
                    d.status = $('#status').val();
                }
            }
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }
                , {
                    data: 'cheque_book.bank_account.account_name'
                    , name: 'cheque_book.bank_account.account_name'
                }
                , {
                    data: 'cheque_book.book_code'
                    , name: 'cheque_book.book_code'
                }
                , {
                    data: 'cheque_book.bank_account.bank.bank_name'
                    , name: 'cheque_book.bank_account.bank.bank_name'
                }
                , {
                    data: 'cheque_book.bank_account.branch.bank_branch_name'
                    , name: 'cheque_book.bank_account.branch.bank_branch_name'
                }
                , {
                    data: 'cheque_number'
                    , name: 'cheque_number'
                },{
                    data: 'referance_no'
                    , name: 'referance_no'
                }
                , {
                    data: 'status'
                    , name: 'status'
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

    $(document).on('click', '.btnView', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/cheque_books/getDetails/' + id
            , method: 'GET'
            , success: function(response) {
                console.log(response);
                $('#view_data').empty();
                $('#view_cheque_date').text(response.book_details?.cheque_date ? new Date(response.book_details.cheque_date).toLocaleDateString('en-GB') : '-');
                $('#view_book_code').text(response.book_details.cheque_book.book_code);
                $('#view_account_name').text(response.book_details.cheque_book.bank_account.account_name);
                $('#view_account_number').text(response.book_details.cheque_book.bank_account.account_no);
                $('#view_bank_name').text(response.book_details.cheque_book.bank_account.bank.bank_name);
                $('#view_branch_name').text(response.book_details.cheque_book.bank_account.branch.bank_branch_name);
                $('#view_vendor_name').text(response.book_details.payment?response.book_details.payment.vendor.name:'-');
                $('#view_payment_category').text(response.book_details.payment?response.book_details.payment.expense.category.name:'-');
                $('#view_payment_sub_category').text(response.book_details.payment?response.book_details.payment.expense.sub_category.name:'-');
                $('#view_isssued_date').text(response.book_details?.issue_date ? new Date(response.book_details.issue_date).toLocaleDateString('en-GB') : '-');
                $('#view_issued_by').text(response.book_details.issued_by?response.book_details.issued_by.name:"-");
                $('#view_cleared_date').text(response.book_details?.clear_date ? new Date(response.book_details.clear_date).toLocaleDateString('en-GB') : '-');
                $('#view_cleared_by').text(response.book_details.cleared_by?response.book_details.cleared_by.name:'-');
                $('#view_canceled_date').text(response.book_details?.cancel_date ? new Date(response.book_details.cancel_date).toLocaleDateString('en-GB') : '-');
                $('#view_cancelled_by').text(response.book_details.cancelled_by?response.book_details.cancelled_by.name:'-');
                $('#view_payment_amount').text(response.book_details.payment?response.book_details.payment.amount:'-');
                $('#layout_cheque_date').text(response.book_details?.cheque_date ? new Date(response.book_details.cheque_date).toLocaleDateString('en-GB') : 'dd/MM/yyyy');
                $('#layout_bank_name').text(response.book_details.cheque_book.bank_account.bank.bank_name);
                $('#layout_cheque_no').text(response.book_details.cheque_book.book_code+'/'+response.book_details.id);
                $('#layout_cheque_bank_code').text(response.book_details.cheque_book.bank_account.bank.bank_code+'/');
                $('#layout_cheque_branch_code').text(response.book_details.cheque_book.bank_account.branch.bank_branch_code);
                // $('#layout_vendor_name').text(response.book_details.payment?response.book_details.payment.vendor.name:'-');
                $('#layout_amount_on_words').text();
                $('#layout_payment_amount').text(response.book_details.payment?response.book_details.payment.amount:'-');
                $('#view_vendor_account').text(response.book_details.payment.expense.vendor_account?response.book_details.payment.expense.vendor_account.account_number +" / "+ response.book_details.payment.expense.vendor_account.mobile :"-");
                $('#layout_cheque_no').text(response.book_details.cheque_number);
                $('#view_cheque_no').text(response.book_details.cheque_number);
                $('#view_nikname').text(response.book_details.cheque_book.nikname);
                $('#view_invoice_no').text(response.book_details.referance_no);
                numberOnWord(response.book_details.payment?response.book_details.payment.amount:'0');
                const status = response.book_details.status;
                let capitalizedStatus="";
                let badgeClass;
                let status_text="";
                if (status === 'pending') {
                    capitalizedStatus = "Available";
                    badgeClass = 'badge bg-primary';
                } else if (status === 'issued') {
                 capitalizedStatus = "Issued";
                    badgeClass = 'badge bg-primary';
                } else if (status === 'cleared') {
                 capitalizedStatus = "Cleared";
                    badgeClass = 'badge bg-success-1';
                } else {
                    capitalizedStatus = "Cancelled";
                    badgeClass = 'badge bg-danger';
                }
                $('#view_status').html(`<span class="${badgeClass}" style="">${capitalizedStatus}</span>`);
                $('#cheque_status').html(`<span class="${badgeClass}" style="">${capitalizedStatus}</span>`);
                let expense_id = response.book_details.payment?response.book_details.payment.expense_id:'';
                toggleSignatureLayout(response.book_details.signatory_id);
                if(response.book_details.validity_period==1){
                    $('#layout_payee_validity').text('VALID ONLY FOR 30 DAYS');
                }else if(response.book_details.validity_period==2){
                $('#layout_payee_validity').text('VALID ONLY FOR 60 DAYS');
                 }else{
                     $('#layout_payee_validity').text('');
                 }
                 if(response.book_details.payment_condition==1){
                    $('#layout_payee').text('A/C PAYEE ONLY');
                }else if(response.book_details.payment_condition==2){
                    $('#layout_payee').text('A/C PAYEE ONLY – NOT NEGOTIABLE');
                 }else{
                     $('#layout_payee').text('');
                 }

                 api_call_back(expense_id, function(data) {
                    if(response.book_details.payee_name==1){
                        if(data.expense.vendor.nic){
                            $('#layout_vendor_name').text(data.expense.vendor.name+" / "+data.expense.vendor.nic);
                        }else{
                            $('#layout_vendor_name').text(data.expense.vendor.name);
                        }
                    }
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
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
    function toggleSignatureLayout(option) {
        // Hide all signature layouts
        document.querySelectorAll('.signature').forEach(el => el.style.display = 'none');
        // Show the selected signature layout
        const selectedSignature = document.getElementById(option);
        if (selectedSignature) {
            selectedSignature.style.display = 'block';
        }
    }
    function numberOnWord(amount) {
    $.ajax({
        url: '/finance/expense/payment/amountOnWords/',
        method: 'GET',
        dataType: 'json',
        data: {
            amount: amount // Use the function parameter here
        },
        success: function(response) {
            if (response.amount_on_words) {
                $('#layout_amount_on_words').text(response.amount_on_words);
            } else {
                $('#layout_amount_on_words').text("Error in response");
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Could not fetch amount in words. Please try again."
            });
        }
    });
}
</script>
@endsection
