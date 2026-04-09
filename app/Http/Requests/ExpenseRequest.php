<?php

namespace App\Http\Requests;

use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'company_id' => ['required'],
            'category_id' => ['required'],
            'sub_category_id' => ['required'],
            'description' => ['required'],
            'expense_date' => ['required'],
            'vendor_id' => ['required'],
            'amount' => [
                'required',
                'numeric',
                'regex:/^\d{1,13}(\.\d{1,2})?$/',
                'max:999999999999.99'
            ]
        ];
    }
}
