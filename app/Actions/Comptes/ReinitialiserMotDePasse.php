<?php

namespace App\Actions\Comptes;

use App\Models\Cliente;
use App\Models\JournalActivite;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ReinitialiserMotDePasse implements ResetsUserPasswords
{
    use ReglesMotDePasse;

    /**
     * @param  Cliente  $cliente
     * @param  array<string, string>  $donnees
     */
    public function reset($cliente, array $donnees): void
    {
        Validator::make($donnees, [
            'password' => $this->reglesMotDePasse(),
        ])->validate();

        $cliente->forceFill([
            'password' => $donnees['password'],
        ])->save();

        JournalActivite::consigner('cliente.mot-de-passe', 'Mot de passe réinitialisé par lien email', $cliente, auteur: $cliente);
    }
}
