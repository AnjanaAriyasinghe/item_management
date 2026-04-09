<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Rules\AccountNoRule;
use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $id = $this->route('company');
        $rules = [
            'system_title' => ['required','string'],
            'name' => ['required  '],
            'description' => ['nullable'],
            'logo' => ['nullable'],
            'address' => ['required'],
            'contact_number' => [
                'nullable',
                'digits:10',
                // function ($attribute, $value, $fail) use ($id) {
                //     // If mobile is provided, check if it's unique
                //     if ($value && Company::where('contact_number', $value)->where('id', '!=', $id)->exists()) {
                //         $fail('The Contact has already been taken.');
                //     }
                // },
            ],
            'mobile' => [
                'nullable',
                'digits:10',
                // function ($attribute, $value, $fail) use ($id) {
                //     // If mobile is provided, check if it's unique
                //     if ($value && Company::where('mobile', $value)->where('id', '!=', $id)->exists()) {
                //         $fail('The mobile number has already been taken.');
                //     }
                // },
            ],
            'pv_no' => ['required  '],
        ];
        return $rules;
    }
}
