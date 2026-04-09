<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NicValidation implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Validate both old (9 digits + V/X) and new (12 digits) NIC formats
        return preg_match('/^\d{9}[VvXx]$|^\d{12}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid NIC, containing exactly 9 digits followed by "V", "v", "X", or "x", or exactly 12 digits.';
    }
}

