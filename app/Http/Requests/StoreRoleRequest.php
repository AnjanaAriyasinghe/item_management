<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'name' => 'required|string|max:250|unique:roles,name',
            'permissions' => 'required',
        ];
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['name'] = 'required|string|max:250|unique:roles,name,' . $this->route('role');
        }
        return $rules;
    }
}
