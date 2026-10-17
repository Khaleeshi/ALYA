<?php

namespace App\Rules;

use App\Support\Telephone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Numéro sénégalais joignable : le compte d'une cliente et ses codes SMS
 * restent au Sénégal au lancement (cahier §1.2).
 */
class TelephoneSenegalais implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Telephone::estSenegalais($value)) {
            $fail(__('validation.telephone_senegal'));
        }
    }
}
