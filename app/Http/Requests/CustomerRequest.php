<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer');

        return [
            'name' => [
                'required',
                'string',
                'max:250',
                Rule::unique('customers', 'name')->ignore($id),
            ],
            'phone' => [
                'nullable',
                'digits:10',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value && Customer::where('phone', $value)->where('id', '!=', $id)->exists()) {
                        $fail('The phone number has already been taken.');
                    }
                },
            ],
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:100',
        ];
    }
}
