<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BankBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = fopen(base_path("database/seeders/banks.csv"), 'r');
        $branches = fopen(base_path("database/seeders/branches.csv"), 'r');

        while (($data = fgetcsv($banks, 20000, ",")) !== FALSE) {
                Bank::updateOrCreate([
                   'bank_code' => $data['0'],
                    'bank_name' => $data['1']
                ]);
        }
        fclose($banks);


        while (($data = fgetcsv($branches, 20000, ",")) !== FALSE) {
                $bank=Bank::where('bank_code',$data['0'])->first();
                if($bank){
                    BankBranch::updateOrCreate([
                        'bank_id' => $bank->id,
                         'bank_branch_code' => $data['1'],
                         'bank_branch_name' => $data['2']
                     ]);
                }
        }
        fclose($branches);
    }
}
