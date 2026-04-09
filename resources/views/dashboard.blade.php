@extends('layouts.main')

@section('title', 'Home')
@section('breadcrumb-item', 'Dashboard')

@section('breadcrumb-item-active', 'Home')

@section('css')
<!-- map-vector css -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/jsvectormap.min.css') }}">

@endsection

@section('content')

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="card statistics-card-1 overflow-hidden ">
            <div class="card-body">
                <img src="{{ URL::asset('build/images/widget/img-status-4.svg') }}" alt="img" class="img-fluid img-bg">
                <h5 class="mb-4">Expense Categories</h5>
                <div class="d-flex align-items-center mt-3">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0"><span id='expens_categories' class="badge bg-light-primary ms-2">0</span></h3>
                    <span class="badge bg-light-success ms-2"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card statistics-card-1 overflow-hidden ">
            <div class="card-body">
                <img src="{{ URL::asset('build/images/widget/img-status-5.svg') }}" alt="img" class="img-fluid img-bg">
                <h5 class="mb-4">Expense Sub Categories</h5>
                <div class="d-flex align-items-center mt-3">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0"><span id='expens_sub_categories' class="badge bg-light-primary ms-2">0</span></h3>

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card statistics-card-1 overflow-hidden ">
            <div class="card-body">
                <img src="{{ URL::asset('build/images/widget/img-status-5.svg') }}" alt="img" class="img-fluid img-bg">
                <h5 class="mb-4">Vendors</h5>
                <div class="d-flex align-items-center mt-3">
                    <h3 class="f-w-300 d-flex align-items-center m-b-0"><span id='venndors' class="badge bg-light-primary ms-2">0</span></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="card statistics-card-1 overflow-hidden  bg-brand-color-3">
            <div class="card-body">
                <img src="{{ URL::asset('build/images/widget/img-status-6.svg') }}" alt="img" class="img-fluid img-bg">
                <h5 class="mb-4 text-white">Bank Accounts </h5>
                <div class="d-flex align-items-center mt-3">
                    <h3 class="text-white f-w-300 d-flex align-items-center m-b-0"><span id='bankAccounts' class="badge bg-light-primary ms-2">0</span></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="card statistics-card-1 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-12 mb-2">
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
                        {{-- <label for="name">Category<span class="text-danger">*</span></label> --}}
                    </div>
                </div>
                <div class="row">

                    <div class="form-group col-md-4 mb-0">
                        <select name="year" id="year" class="form-control year form-control-custom-select">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <select name="selector" id="selector" class="form-control selector form-control-custom-select">
                            <option value="monthly">Monthly</option>
                            <option value="daily">Daily</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0" id="monthly-div">
                        <select name="monthly" id="monthly" class="form-control monthly form-control-custom-select">
                            <option value="">--select month--</option>
                            @foreach($months as $value)
                            <option value="{{ $value }}" @if($value==$currentMonth) selected @endIf>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0" style="display:none;" id="daily-div">
                        <input class="mb-0 form-control form-control-custom" type="date" id="daily" name="daily" onchange="dateClicked()" placeholder="">
                    </div>
                </div>
                <div class="container">
                    <h2>Expenses</h2>
                    <div>
                        <canvas id="expense_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="card statistics-card-1 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-12 mb-2">
                        <select name="search_payments_company_id" id="search_payments_company_id" class="form-control search_payments_company_id form-control-custom-select">
                            <option value=""></option>
                            <option value="all">All</option>
                            @foreach ($companies as $company)
                                {{-- <option value="{{ $company->id }}">{{ $company->name }}</option> --}}
                                <option value="{{ $company->id }}" {{ $company->id == $defaultCompany ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- <label for="name">Category<span class="text-danger">*</span></label> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4 mb-0">
                        <select name="year_payment" id="year_payment" class="form-control year_payment form-control-custom-select">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <select name="selector_payment" id="selector_payment" class="form-control selector_payment form-control-custom-select">
                            <option value="monthly">Monthly</option>
                            <option value="daily">Daily</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0" id="monthly-div_payment">
                        <select name="monthly_payment" id="monthly_payment" class="form-control monthly_payment form-control-custom-select">
                            <option value="">--select month--</option>
                            @foreach($months as $value)
                            <option value="{{ $value }}" @if($value==$currentMonth) selected @endIf>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0" style="display:none;" id="daily-div_payment">
                        <input class="mb-0 form-control form-control-custom" type="date" id="daily_payment" name="daily_payment" onchange="dateClicked_payment()" placeholder="">
                    </div>
                </div>
                <div class="container">
                    <h2>Payments</h2>
                    <div>
                        <canvas id="payments_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12">
        <div class="card statistics-card-1 overflow-hidden">
            <div class="card-body">
                <div class="col-md-6 col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between py-3">
                            <h5>Recent Payments</h5>
                        </div>
                        <div class="card-body table-border-style">
                            <div class="table-responsive">
                                 <div class="row mb-3">
                                    <div class="col-md-4 ">
                                        <div class="form-group form-floating mb-0">
                                            <select name="search_company_id_recent_payments" id="search_company_id_recent_payments" class="form-control search_company_id_recent_payments form-control-custom-select">
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
                                <table class="table table-sm data-table">
                                    <thead>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Company</th>
                                        <th>Vendor</th>
                                        <th>Category</th>
                                        <th>Sub category</th>
                                        <th>Date</th>
                                        <th>status</th>
                                        <th>Amount</th>
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
    </div>
</div>
</div>

<!-- [ Main Content ] end -->
@endsection

@section('scripts')
<!-- [Page Specific JS] start -->
{{-- <script src="{{ URL::asset('build/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/jsvectormap.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/world.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/world-merc.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/dashboard-default.js') }}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    $(function() {
        var table = $('.data-table').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">'
            , buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
            , processing: true
            , serverSide: true
            // , ajax: "{{ route('dashboard')}}"
            ,ajax: {
                    url: "{{ route('dashboard')}}",
                    data: function(d) {
                        d.company_id = $('#search_company_id_recent_payments').val();
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
                }
                , {
                    data: 'code'
                    , name: 'code'
                },
                {
                    data: 'company'
                    , name: 'company'
                },
                {
                    data: 'vendor.name'
                    , name: 'vendor.name'
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
                    data: 'payment_date'
                    , name: 'payment_date'
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
                    data: 'amount'
                    , name: 'amount'
                }
            ]
        , });
        $("#search").on("click", function() {
            table.draw();
        });
    });
    $(document).ready(function() {
        $.ajax({
            url: '/dashboard_data'
            , method: 'GET'
            , success: function(response) {
                $('#bankAccounts').text(response.data.bankAccounts);
                $('#expens_categories').text(response.data.expens_categories);
                $('#expens_sub_categories').text(response.data.expens_sub_categories);
                $('#venndors').text(response.data.venndors);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });

        var company_id = $('#search_payments_company_id').val();
        var year = $('#year').val();
        var selector = $('#selector').val();
        var monthly = $('#monthly').val();
        var daily = $('#daily').val();
        expense_chart_expenses(company_id,year, selector, monthly, daily);
        var company_id = $('#search_company_id').val();
        var year = $('#year_payment').val();
        var selector = $('#selector_payment').val();
        var monthly = $('#monthly_payment').val();
        var daily = $('#daily_payment').val();
        payment_chart_expenses(company_id, year, selector, monthly, daily);
    });

    $(document).on('change', '.selector', function() {
        var selector = $(this).val();
        $('#daily').val('');
        $('#monthly').val('');

        $('#daily-div').hide()
        $('#monthly-div').hide();
        if (selector == "monthly") {
            $('#monthly-div').show();
            $('#daily-div').hide();
        } else if (selector == "daily") {
            $('#daily-div').show();
            $('#monthly-div').hide();
        }
    });
    $(document).on('change', '.selector_payment', function() {
        var selector = $(this).val();
        $('#_payment').val('');
        $('#monthly_payment').val('');

        $('#daily-div_payment').hide()
        $('#monthly-div_payment').hide();
        if (selector == "monthly") {
            $('#monthly-div_payment').show();
            $('#daily-div_payment').hide();
        } else if (selector == "daily") {
            $('#daily-div_payment').show();
            $('#monthly-div_payment').hide();
        }
    });

    $(document).on('change', '.monthly', function() {
        var year = $('#year').val();
        var selector = $('#selector').val();
        var monthly = $('#monthly').val();
        var daily = $('#daily').val();
        expense_chart_expenses(year, selector, monthly, daily);
    });
    $(document).on('change', '.weekly', function() {
        var year = $('#year').val();
        var selector = $('#selector').val();
        var monthly = $('#monthly').val();
        var weekly = $('#weekly').val();
        var daily = $('#daily').val();
        expense_chart_expenses(year, selector, monthly, daily);
    });

    $(document).on('change', '.monthly_payment', function() {
        var year = $('#year_payment').val();
        var selector = $('#selector_payment').val();
        var monthly = $('#monthly_payment').val();
        var daily = $('#daily_payment').val();
        payment_chart_expenses(year, selector, monthly, daily);
    });

    $(document).on('change', '.weekly_payment', function() {
        var year = $('#year_payment').val();
        var selector = $('#selector_payment').val();
        var monthly = $('#monthly_payment').val();
        var daily = $('#daily_payment').val();
        payment_chart_expenses(year, selector, monthly, daily);
    });

    function dateClicked_payment() {
        var company_id = $('#search_payments_company_id').val();
        var year = $('#year_payment').val();
        var selector = $('#selector_payment').val();
        var monthly = $('#monthly_payment').val();
        var daily = $('#daily_payment').val();
        payment_chart_expenses(company_id, year, selector, monthly, daily);
    }

    function dateClicked() {
        var company_id = $('#search_company_id').val();
        var year = $('#year').val();
        var selector = $('#selector').val();
        var monthly = $('#monthly').val();
        var weekly = $('#weekly').val();
        var daily = $('#daily').val();
        expense_chart_expenses(company_id,year, selector, monthly, daily);
    }

    function expense_chart_expenses(company_id, year, selector, monthly, daily) {
        $.ajax({
            url: '/dashboard/expenses_chart'
            , method: 'GET'
            , data: {
                company_id: company_id
                , year: year
                , selector: selector
                , monthly: monthly
                , daily: daily
            }
            , success: function(response) {
                // Get the canvas element
                var ctx = document.getElementById("expense_chart").getContext('2d');
                // Check if the chart exists and destroy it if it does
                if (window.expense_chart instanceof Chart) {
                    window.expense_chart.destroy(); // Destroy the chart if it's an instance of Chart
                }
                window.expense_chart = new Chart(ctx, {
                    type: 'bar'
                    , data: {
                        labels: response.labels, // Category names
                        datasets: [{
                            label: "Expenses",
                            backgroundColor: response.colors, // Colors for each slice
                            data: response.data // Total amounts
                        }]
                    }
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    }

    function payment_chart_expenses(company_id, year, selector, monthly, daily) {
        $.ajax({
            url: '/dashboard/payment_chart'
            , method: 'GET'
            , data: {
                company_id: company_id
                , year: year
                , selector: selector
                , monthly: monthly
                , daily: daily
            }
            , success: function(response) {
                // Get the canvas element
                var ctx = document.getElementById("payments_chart").getContext('2d');
                // Check if the chart exists and destroy it if it does
                if (window.payments_chart instanceof Chart) {
                    window.payments_chart.destroy(); // Destroy the chart if it's an instance of Chart
                }
                window.payments_chart = new Chart(ctx, {
                    type: 'bar'
                    , data: {
                        labels: response.labels, // Category names
                        datasets: [{
                            label: "Payments",
                            backgroundColor: response.colors, // Colors for each slice
                            data: response.data // Total amounts
                        }]
                    }
                });
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    }
</script>
@endsection
