<?php

namespace App\Filament\Pages\Auth;

use App\Listeners\JournaliserAuthentification;
use App\Support\Securite\VerrouillageConnexion;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;
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
        $cle = VerrouillageConnexion::cle('personnel', $email, request()->ip());

        if (RateLimiter::tooManyAttempts($cle, VerrouillageConnexion::ESSAIS)) {
            $this->notifierVerrouillage(RateLimiter::availableIn($cle));

            return null;
        }

        try {
            $reponse = parent::authenticate();
        } catch (ValidationException $exception) {
            RateLimiter::hit($cle, VerrouillageConnexion::DUREE_SECONDES);

            if (RateLimiter::tooManyAttempts($cle, VerrouillageConnexion::ESSAIS)) {
                JournaliserAuthentification::consignerVerrouillage('administration', $email);
            }

            throw $exception;
        }

        if ($reponse !== null) {
            RateLimiter::clear($cle);
        }

        return $reponse;
    }

    protected function notifierVerrouillage(int $secondes): void
    {
        Notification::make()
            ->title(__('auth.throttle', ['seconds' => $secondes, 'minutes' => (int) ceil($secondes / 60)]))
            ->danger()
            ->send();
    }
}
