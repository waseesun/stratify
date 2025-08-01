<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class IsCompanyUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  
     * @param  mixed  
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The failure callback.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);

        if (!$user || $user->role !== 'company') {
            $fail("The selected :attribute must belong to a user with the 'company' role.");
        }
    }
}