<?php

namespace App\Http\Responses\Comptes;

use App\Support\Langues;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;

/**
 * Réponses des écrans de compte : une redirection vers une page de la
 * vitrine (ou la page précédente), dans la langue de l'adresse, avec un
 * message facultatif.
 */
class ReponsesComptes implements FailedPasswordResetLinkRequestResponse, LoginResponse, LogoutResponse, PasswordResetResponse, RegisterResponse, SuccessfulPasswordResetLinkRequestResponse
{
    public function __construct(
        private readonly ?string $route,
        private readonly ?string $message = null,
    ) {}

    /**
     * @param  Request  $request
     */
    public function toResponse($request): RedirectResponse
    {
        $langue = Langues::depuisRequete($request);

        $reponse = match ($this->route) {
            null => back(),
            'mon-compte' => redirect()->intended(route_langue('mon-compte', langue: $langue)),
            default => redirect(route_langue($this->route, langue: $langue)),
        };

        return $this->message === null ? $reponse : $reponse->with('statut', trans($this->message, [], $langue));
    }
}
