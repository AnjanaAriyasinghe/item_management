<?php

namespace Database\Seeders;

use App\Models\AuthorizedSignatory;
use Illuminate\Database\Seeder;

class AuthorizedSignatorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array=[
         'Director',
         'Director – Director',
         'Director – Director – Director',
         'Director – Authorized Signatory',
         'Director – Director – Authorized Signatory',
        ];
            foreach ( $array as $a) {
                AuthorizedSignatory::updateOrCreate([
                    'name' => $a,
                    'is_active' =>'1',
                    'created_by'=>1,
                ]);
            }
    }
}
