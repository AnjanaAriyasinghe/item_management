<?php

namespace App\Http\Requests;

use App\Rules\NicValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdatePasswordRequest extends FormRequest
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
        $rules = [
            'user_id' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Passwords do not match',
        ];
    }
}
