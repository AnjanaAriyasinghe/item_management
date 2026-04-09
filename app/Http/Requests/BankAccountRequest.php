<?php

namespace App\Http\Requests;

use App\Rules\AccountNoRule;
use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountRequest extends FormRequest
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
        $id = $this->route('bank_account');
        $rules = [
            'account_name' => ['required','string'],
            'account_no' => ['required',new AccountNoRule(),Rule::unique('bank_accounts','account_no')->whereNull('deleted_at')->ignore($id)],
            'bank_id' => 'required',
            'branch_id' => 'required',
        ];
        return $rules;
    }
}
