<!-- [ Header Topbar ] start -->
<style>
    .header-wrapper {
        background-color: rgb(229, 243, 242);
         !important;
    }

</style>
<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown pc-h-item header-user-profile me-4" >
                    <div class="dropdown ms-sm-3 header-item topbar-user">

                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                    {{ optional(Auth::user()->defaultCompany)->name ?? 'No Default Company' }}
                                </span>
                            </span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="page-header-user-dropdown">
                            @if (Auth::user()->companies->count())
                                @foreach (Auth::user()->companies as $company)
                                    <form method="POST" action="{{ route('admin.users.update_default_company', Auth::user()->id) }}">
                                        @csrf
                                        <input type="hidden" name="default_company" value="{{ $company->id }}">
                                        <button class="dropdown-item" type="submit">
                                            {{ $company->name }}
                                        </button>
                                    </form>
                                @endforeach
                            @else
                                <a class="dropdown-item" href="#">No Branches Available</a>
                            @endif
                        </div>

                    </div>
                </li>

                <li class="dropdown pc-h-item header-user-profile">
                    <span class="text-dark">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->image)
                    @php
                    $imageUrl = asset('storage/' . auth()->user()->image);
                    @endphp
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{  $imageUrl }}" alt="user-image" class="user-avtar">
                    </a>
                    @else
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{ URL::asset('build/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar">
                    </a>
                    @endif
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Profile</h5>
                        </div>
                        <div class="dropdown-body">
                            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                <ul class="list-group list-group-flush w-100">
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                @if(auth()->user()->image)
                                                @php
                                                $imageUrl = asset('storage/' . auth()->user()->image); // Store the image URL in a variable
                                                @endphp
                                                <img src="{{ $imageUrl }}" alt="user-image" class="wid-50 rounded-circle" />
                                                @else
                                                <img src="{{ asset('build/images/user/avatar-2.jpg') }}" alt="user-image" class="wid-50 rounded-circle" />
                                                @endif
                                            </div>

                                            <div class="flex-grow-1 mx-3">
                                                <h5 class="mb-0">{{ auth()->user()->name }}</h5>
                                                <a class="link-primary" href="#"></a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{route('user.reset.index')}}" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-key"></i>
                                                <span>Change password</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="#" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-plus-circle"></i>
                                                <span>Add account</span>
                                            </span>
                                        </a>
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                      document.getElementById('logout-form').submit();" class="dropdown-item">
                                            <span class="d-flex align-items-center">
                                                <i class="ph-duotone ph-power"></i>
                                                <span>Logout</span>
                                            </span>
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->
