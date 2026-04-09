<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superAdmin@vitalone.lk',
            'password' => Hash::make('123')
        ]);
        $superAdmin->assignRole('Super Admin');

        // Creating Admin User
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@vitalone.lk',
            'password' => Hash::make('123')
        ]);
        $admin->assignRole('Admin');

        // Creating Product Manager User
        $productManager = User::create([
            'name' => 'Manager',
            'email' => 'Manager@vitalone.lk',
            'password' => Hash::make('123')
        ]);
        $productManager->assignRole('Manager');

        Company::create([
            'system_title'=>'Cheque Printing Application',
            'name'=>'****',
            'description'=>'*********',
            'logo'=>'*****',
            'address'=>'*****',
            'contact_number'=>'00000000',
            'mobile'=>'00000000',
        ]);
    }
}
