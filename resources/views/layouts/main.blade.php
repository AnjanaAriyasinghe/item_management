    <!DOCTYPE html>
    <html lang="en">

    <head>
        @php
        use App\Models\Company;
        $company = Company::first(); // Fetch the first company record
        @endphp
        <title>@yield('title') |{{ $company?$company->system_title:"Stock Management" }} </title>
        <!-- [Meta] -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta />
        <meta name="author" content="phoenixcoded" />

        <!-- [Favicon] icon -->
        <link rel="icon" href="" type="image/x-icon">
        @yield('css')

        @include('layouts.head-css')
    </head>

    <body data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
        @include('layouts.loader')
        @include('layouts.sidebar')
        @include('layouts.topbar')

        <!-- [ Main Content ] start -->
        <div class="pc-container">
            <div class="pc-content">
                @if(View::hasSection('breadcrumb-item'))
                @include('layouts.breadcrumb')
                @endif
                <!-- [ Main Content ] start -->
                @yield('content')
                <!-- [ Main Content ] end -->
            </div>
        </div>
        <!-- [ Main Content ] end -->

        @include('layouts.footer')
        @include('layouts.customizer')

        @include('layouts.footerjs')

        @yield('scripts')

    </body>
    <!-- [Body] end -->
    </html>
