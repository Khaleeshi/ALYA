<?php

namespace App\Actions\Comptes;

use Illuminate\Validation\Rules\Password;

trait ReglesMotDePasse
{
    /**
     * @return array<int, mixed>
     */
    protected function reglesMotDePasse(): array
    {
        return ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'];
    }
}
