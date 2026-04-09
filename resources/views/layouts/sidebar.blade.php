<!-- [ Sidebar Menu ] start -->

<nav class="pc-sidebar">
    <div class="navbar-wrapper">

        <div class="m-header text-center">
            @php
            use App\Models\Company;
            $company = Company::first(); // Fetch the first company record
            @endphp
            <a href="/dashboard" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="https://vitalone.lk/images/resources/logo-dark.png" alt="VitalOne" style="position: relative; bottom: 3px; left: 4px;" width="80px">
                {{-- <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" width="60%"> --}}
                {{-- <img class="ps-2" src="{{ asset('storage/company_logo/vitalone-dark-logo.png') }}" alt="Company Logo" width="100%"> --}}

            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar">
                @include('layouts.menu-list')
            </ul>
        </div>

    </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
