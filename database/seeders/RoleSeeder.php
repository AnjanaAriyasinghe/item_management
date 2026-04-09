<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Admin']);
        $productManager = Role::create(['name' => 'Manager']);

        $admin->givePermissionTo([
            'admin-common-users-module',
            'admin-common-users-create',
            'admin-common-users-edit',
            'admin-common-users-delete',
            'admin-common-user_roles-module',
            'admin-common-user_roles-create',
            'admin-common-user_roles-edit',
            'admin-common-user_roles-delete'

        ]);

        $productManager->givePermissionTo([
            'admin-common-users-module',
            'admin-common-users-create',
            'admin-common-users-edit',
            'admin-common-users-delete',
        ]);
    }
}
