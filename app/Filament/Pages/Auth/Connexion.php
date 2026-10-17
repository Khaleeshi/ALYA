<?php

namespace App\Filament\Pages\Auth;

use App\Listeners\JournaliserAuthentification;
use App\Support\Securite\VerrouillageConnexion;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Connexion à l'administration, avec le même verrouillage que la boutique :
 * cinq échecs (mot de passe ou code de double authentification) pour la même
 * adresse email depuis la même adresse IP bloquent la connexion quinze minutes.
 */
class Connexion extends Login
{
    public function authenticate(): ?LoginResponse
    {
        $email = (string) ($this->data['email'] ?? '');
        $cle = VerrouillageConnexion::cle('administration', $email, request()->ip());

        if (VerrouillageConnexion::estVerrouille($cle)) {
            Notification::make()
                ->title(VerrouillageConnexion::message($cle))
                ->danger()
                ->send();

            return null;
        }

        try {
            $reponse = parent::authenticate();
        } catch (ValidationException $exception) {
            if (VerrouillageConnexion::echec($cle)) {
                JournaliserAuthentification::consignerVerrouillage('administration', $email);
            }

            throw $exception;
        }

        if ($reponse !== null) {
            VerrouillageConnexion::reussite($cle);
        }

        return $reponse;
    }
}
