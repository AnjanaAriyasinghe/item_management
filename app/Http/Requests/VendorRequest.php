<?php

namespace App\Http\Requests;

use App\Models\VenderHasAccount;
use App\Models\Vendor;
use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
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
        $id = $this->route('vendor');

        $rules = [
            'name' => [
                'required',
                'string',
                'max:250',
                Rule::unique('vendors', 'name')->ignore($id)
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:250',
                Rule::unique('vendors', 'email')->ignore($id)
            ],
            'phone' => [
                'nullable',
                'digits:10',
                function ($attribute, $value, $fail) use ($id) {
                    // If phone is provided, check if it's unique
                    if ($value && Vendor::where('phone', $value)->where('id', '!=', $id)->exists()) {
                        $fail('The contact number has already been taken.');
                    }
                },
            ],
            'mobile' => [
                'nullable',
                'digits:10',
                function ($attribute, $value, $fail) use ($id) {
                    // If mobile is provided, check if it's unique
                    if ($value && Vendor::where('mobile', $value)->where('id', '!=', $id)->exists()) {
                        $fail('The mobile number has already been taken.');
                    }
                },
            ],
            'address' => 'nullable',
            'nic' => [
                'nullable',
                'string',
                'max:12',
                'min:9',
                new NicValidation(),
                Rule::unique('vendors', 'nic')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
            'br_no' => [
                'nullable',
                'string',
                'max:50',
                'min:5',
                Rule::unique('vendors', 'br_no')->where(function ($query) use ($id) {
                    return $query->where('id', '!=', $id);
                })
            ],
            'mobile_numbers' => [
                'array', // Ensure the input is an array
                function ($attribute, $value, $fail) {
                    if (count($value) !== count(array_unique($value))) {
                        $fail('All mobile numbers must be different.');
                    }
                },
            ],
            'mobile_numbers.*' => [
                'nullable',
                'digits:10',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value && VenderHasAccount::where('mobile', $value)->where('vendor_id', '!=', $id)->exists()) {
                        $fail("The mobile number $value has already been taken.");
                    }
                },
            ],
            'account_numbers' => ['nullable', 'array'],
            'account_numbers.*' => [
                'nullable',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value && VenderHasAccount::where('account_umber', $value)->where('vendor_id', '!=', $id)->exists()) {
                        $fail("The account number $value has already been taken.");
                    }
                },
            ],
            'account_numbers.*' => [
                function ($attribute, $value, $fail) use ($id) {
                    $accountNumbers = $this->input('account_numbers', []);

                    // Check if all account numbers are the same
                    if (count($accountNumbers) !== count(array_unique($accountNumbers))) {
                        $fail('All account numbers must be different.');
                    }
                }
            ],
        ];

        // Custom logic to check if either mobile or phone is unique
        if ($this->input('mobile') && $this->input('phone')) {
            // If both mobile and phone are provided, ensure they're unique
            $rules['phone'][] = function ($attribute, $value, $fail) use ($id) {
                $mobile = $this->input('mobile');
                if ($mobile && $value && $value === $mobile) {
                    $fail('The contct and mobile numbers cannot be the same.');
                }
            };
        }

        return $rules;
    }
}
