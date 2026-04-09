<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
        $id = $this->route('payment');
        $rules = [
            'bank_id' => ['required'],
            'expense_id' => ['required'],
            'bank_account_id' => ['required'],
            'cheque_book_id' => ['required'],
            'amount' => 'required|numeric|min:0|lte:expense_amount',
            'expense_amount' => 'required|numeric|min:0',
            'cheque_date' => ['required'],
            'cheque_book_detail_id' => ['required'],
            'signatory_id'=>['required'],
            'cheque_book_detail_id'=>['required'],
            'payee_name'=>['required'],
            'payment_condition'=>['required'],
            'validity_period'=>['required'],

        ];

        $messages = [
            'amount.lte' => 'Paid amount cannot be greater than the expense amount.',
        ];

        return $rules;
    }

}
