<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expense_categories = fopen(base_path("database/seeders/expense_categories.json"), 'r');
        $data = json_decode(fread($expense_categories, filesize(base_path("database/seeders/expense_categories.json"))), true);

        foreach ($data['expense_categories'] as $category) {
            $expenseCategory = ExpenseCategory::updateOrCreate([
                'name' => $category['category'],
                'created_by'=>1,
            ]);
            foreach ($category['sub_categories'] as $subCategory) {
                ExpenseSubCategory::updateOrCreate([
                    'name' => $subCategory,
                    'category_id' => $expenseCategory->id,
                    'created_by'=>1,
                ]);
            }
        }
    }
}
