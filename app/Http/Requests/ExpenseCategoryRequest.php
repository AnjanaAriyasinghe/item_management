<?php

namespace App\Http\Requests;

use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('expense_category'); // or 'expense_category'
        return [
            'name' => [
                'required',
                'string',
                'max:250',
                Rule::unique('expense_categories', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at'), // Exclude soft-deleted records
            ],
        ];
    }

}
