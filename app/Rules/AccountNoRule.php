<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AccountNoRule implements Rule
{
    public function passes($attribute, $value)
    {
        // Validate that the value is between 1 and 12 digits
        return preg_match('/^\d{6,20}$/', $value);
    }

    public function message()
    {
        return 'The :attribute must be a number with a minimum of 6 and a maximum of 20 digits.';
    }
}

