<?php

namespace App\Http\Requests;

use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($userId)
            ],
            'mobile' => [
                'required',
                'regex:/^\d{10}$/',
                Rule::unique('users', 'mobile')->whereNull('deleted_at')->ignore($userId)
            ],
            'address' => 'required',
            'nic' => [
                'string',
                'max:12',
                'min:9',
                new NicValidation(),
                Rule::unique('users', 'nic')->where(function ($query) use ($userId) {
                    return $query->where('id', '!=', $userId);
                })
            ],
            'default_company' => ['required'],
            'companies' => ['required','exists:companies,id']
        ];
        return $rules;
    }
}
