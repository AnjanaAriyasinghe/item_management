@extends('layouts.main')

@section('title', 'Report')
@section('breadcrumb-item', 'Report')
@section('breadcrumb-item-active', 'Payments')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Payments</h4>
            </div>
            <div class="card-body">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group form-floating mb-0">
                                    <select name="vendor_id" id="vendor_id" class="form-control vendor_id form-control-custom-select">
                                        <option value=""></option>
                                    </select>
                                    <label for="name">Vendor</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-floating mb-0">
                                    <select name="cheque_book_id" id="cheque_book_id" class="form-control cheque_book_id form-control-custom-select">
                                        <option value=""></option>
                                    </select>
                                    <label for="name">Cheque Book</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-floating mb-0">
                                    <select name="category_id" id="category_id" class="form-control category_id form-control-custom-select">
                                        <option value=""></option>
                                    </select>
                                    <label for="name">Category</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-floating mb-0">
                                    <select name="sub_category_id" id="sub_category_id" class="form-control sub_category_id form-control-custom-select">
                                        <option value=""></option>
                                    </select>
                                    <label for="name">Sub Category</label>
                                </div>
                            </div>
                            <div class="col-md-2  mt-2">
                                <div class="form-group form-floating mb-0">
                                    <select name="status" id="status" class="form-control status form-control-custom-select">
                                        <option value=""></option>
                                        <option value="all">All</option>
                                        <option value="pending">Available</option>
                                        <option value="issued">Issued</option>
                                        <option value="passed">Passed</option>
                                        <option value="reject">Rejected</option>
                                    </select>
                                    <label for="name">Status</label>
                                </div>
                            </div>
                            <div class="col-md-2 mt-2">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="date" id="from_date" name="from_date" placeholder="">
                                    <label for="from_date">From Date</label>
                                </div>
                            </div>
                            <div class="col-md-2 mt-2">
                                <div class="form-group form-floating mb-0">
                                    <input class="mb-0 form-control form-control-custom" type="date" id="to_date" name="to_date" placeholder="">
                                    <label for="to_date">To Date</label>
                                </div>
                            </div>
                            <div class="col-md-2 mt-3">
                                <button type="button" class="btn btn-info" id="search">Search</button>
                            </div>
                        </div>
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                                <th>#</th>
                                <th>Code</th>
                                <th>Company</th>
                                <th>Expense Code</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Vendor</th>
                                <th>Cheque Book</th>
                                <th>Cheque Number</th>
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Cheque Date</th>
                                <th style="text-align:right">Amount</th>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <th colspan="11" style="text-align:right">Total: </th>
                                <th></th>
                            </tfoot>
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
            , ajax: {
                url: "{{ route('report.payments') }}"
                , data: function(d) {
                    d.cheque_book_id = $('#cheque_book_id').val();
                    d.category_id = $('#category_id').val();
                    d.sub_category_id = $('#sub_category_id').val();
                    d.status = $('#status').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.vendor_id = $('#vendor_id').val();
                    d.company_id = $('#search_company_id').val();
                }
            }
            , columns: [{
                    data: 'id'
                    , name: 'id'
                }, {
                    data: 'code'
                    , name: 'code'
                },{
                    data: 'company'
                    , name: 'company'
                }
                , {
                    data: 'expense.code'
                    , name: 'expense.code'
                }
                , {
                    data: 'expense.category.name'
                    , name: 'expense.category.name'
                }
                , {
                    data: 'expense.sub_category.name'
                    , name: 'expense.sub_category.name'
                }
                , {
                    data: 'vendor.name'
                    , name: 'vendor.name'
                }
                , {
                    data: 'cheque_book.book_code'
                    , name: 'cheque_book.book_code'
                }
                , {
                    data: 'cheque_number'
                    , name: 'cheque_number'
                }
                , {
                    data: 'status'
                    , name: 'status'
                }
                , {
                    data: 'payment_date'
                    , name: 'payment_date'
                    , render: function(data) {
                        // Convert the data to a Date object and format it
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US'); // You can customize the locale
                    }
                }
                , {
                    data: 'cheque_date'
                    , name: 'cheque_date'
                    , render: function(data) {
                        // Convert the data to a Date object and format it
                        var date = new Date(data);
                        return date.toLocaleDateString('en-US'); // You can customize the locale
                    }
                }
                , {
                    data: 'amount'
                    , name: 'amount'
                }
            ]
            , footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var totalLoanAmount = api
                    .column(11, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(acc, val) {
                        return acc + parseFloat(val);
                    }, 0);
                $(api.column(11).footer()).html(totalLoanAmount.toFixed(2));
            }

        });
        $("#search").on("click", function() {
            table.draw();
        });
    });
    $(document).ready(function() {
        $.ajax({
            url: '/admin/getcategory'
            , method: 'GET'
            , success: function(response) {
                $('#category_id').empty();
                $("#category_id ").append($("<option />"));
                $("#category_id").append($("<option />").val("all").text("All"));
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
                        .text(option.book_code));
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
        $.ajax({
            url: '/admin/getVendors'
            , method: 'GET'
            , success: function(response) {
                $('#vendor_id').empty();
                $("#vendor_id ").append($("<option />"));
                $("#vendor_id").append($("<option />").val("all").text("All"));
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
    });
    $(document).on('change', '.category_id', function() {
        let id = $(this).val();
        $.ajax({
            url: '/finance/get_sub_category/' + id
            , method: 'GET'
            , success: function(response) {
                $('#sub_category_id').empty();
                $("#sub_category_id ").append($("<option />"));
                $("#sub_category_id").append($("<option />").val("all").text("All"));
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

</script>
@endsection
