<?php

namespace App\Http\Requests;

use App\Rules\AccountNoRule;
use App\Rules\NicValidation;
use App\Rules\UniqueStartNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChequeBookRequest extends FormRequest
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
        $id = $this->route('cheque_book'); // Get the route parameter for the current cheque_book ID

        return [
            'bank_account_id' => ['required', 'string'],
            'number_of_cheque' => ['required', 'integer', 'min:1', 'max:100'],
            'start_number' => [
                'required',
                'numeric',
                'min:0',
                new UniqueStartNumber($this->input('bank_account_id')), // Use $this->input() to get the bank_account_id
            ],
            'account_number' => ['required'],
            'nikname' => [
                'required',
                'string',
                'max:250',
                Rule::unique('cheque_books', 'nikname')->ignore($id), // Ensure $id is used to ignore the current record during updates
            ],
        ];
    }

}
