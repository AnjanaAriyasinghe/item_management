<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Users
            'admin-common-users-module',
            'admin-common-users-create',
            'admin-common-users-edit',
            'admin-common-users-delete',
            'admin-common-users-view',

            // User Roles
            'admin-common-user_roles-module',
            'admin-common-user_roles-create',
            'admin-common-user_roles-edit',
            'admin-common-user_roles-delete',
            'admin-common-user_roles-view',

            // Vendors
            'admin-common-vendor-module',
            'admin-common-vendor-create',
            'admin-common-vendor-edit',
            'admin-common-vendor-delete',
            'admin-common-vendor-view',
            'admin-common-vendor-approve',
            'admin-common-vendor-deactivate',

            // Company
            'admin-common-company-module',
            'admin-common-company-create',
            'admin-common-company-edit',
            'admin-common-company-view',
            'admin-common-company-delete',

            // Bank Accounts
            'admin-common-bank_account-module',
            'admin-common-bank_account-create',
            'admin-common-bank_account-edit',
            'admin-common-bank_account-delete',
            'admin-common-bank_account-view',

            // Items
            'admin-common-items-module',
            'admin-common-items-create',
            'admin-common-items-edit',
            'admin-common-items-delete',
            'admin-common-items-view',

            // Stocks
            'admin-common-stocks-module',
            'admin-common-stocks-create',
            'admin-common-stocks-edit',
            'admin-common-stocks-delete',
            'admin-common-stocks-view',

            // Stock Reports (Reports menu)
            'admin-common-stocks-report-view',
        ];

          // Looping and Inserting Array's Permissions into Permission Table
         foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
          }
    }
}
