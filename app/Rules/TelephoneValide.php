<?php

namespace App\Rules;

use App\Support\Telephone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneValide implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || Telephone::normaliser($value) === null) {
            $fail(__('validation.telephone'));
        }
    }
}
