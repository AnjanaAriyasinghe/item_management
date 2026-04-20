<?php

namespace App\Http\Requests;

use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $userId = $this->route('user');
        $rules = [
            'name' => 'required|string|max:250',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:250',
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required',
            'mobile' => ['required','regex:/^\d{10}$/', Rule::unique('users', 'mobile')->whereNull('deleted_at')],
            'address' => 'required',
            'nic' => [
                'unique:users,nic',
                'string',
                'max:12',
                'min:9',
                Rule::unique('users', 'nic')->whereNull('deleted_at'),
                new NicValidation()
            ],
            'default_company' => ['nullable'],
//            'companies' => ['required','exists:companies,id']
        ];
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['email'] = 'required|string|email:rfc,dns|max:250|unique:users,email,' . $this->route('user');
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Passwords do not match',
        ];
    }
}
