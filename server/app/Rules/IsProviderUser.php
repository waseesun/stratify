<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class IsProviderUser implements ValidationRule
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

        if (!$user || !$user->isProvider()) {
            $fail("The user must belong to the 'provider' role.");
        }
    }
}