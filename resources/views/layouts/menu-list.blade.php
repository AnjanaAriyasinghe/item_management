<li class="pc-item pc-caption">
    <label>Navigation</label>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-gauge"></i>
        </span>
        <span class="pc-mtext">Dashboard</span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/dashboard">Analytics</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-user-circle-gear"></i>
        </span>
        <span class="pc-mtext">Admin</span>
    </a>
    <ul class="pc-submenu">
        @canany(['admin-common-user_roles-module'])
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.roles.index') }}">Roles</a></li>
        @endcanany

        @canany(['admin-common-users-module'])
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.users.index') }}">Users</a></li>
        @endcanany

        <!-- @canany(['admin-common-vendor-module'])
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.vendors.index') }}">Vendors</a></li>
        @endcanany -->

        <!-- @canany(['admin-common-bank_account-module'])
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.bank_accounts.index') }}">Bank Accounts</a></li>
        @endcanany -->

        @canany(['admin-common-items-module'])
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.items.index') }}">Items</a></li>
        @endcanany

        @canany(['admin-common-stocks-module'])
        <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link">Stock<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
            <ul class="pc-submenu">
                <li class="pc-item"><a class="pc-link" href="{{ route('admin.stocks.index') }}">Add Items to Stock</a></li>
            </ul>
        </li>
        @endcanany
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-chart-line-up"></i>
        </span>
        <span class="pc-mtext">Reports</span>
    </a>
    <ul class="pc-submenu">
        @canany(['admin-common-stocks-report-view'])
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.stock_report.index') }}">
                <i class="ti ti-stack-2 me-1"></i>Stock Report</a>
        </li>
        @endcanany
    </ul>
</li>
