<?php

namespace App\Providers;

use App\Actions\Comptes\CreerCliente;
use App\Actions\Comptes\ReinitialiserMotDePasse;
use App\Http\Responses\Comptes\ReponsesComptes;
use App\Models\Cliente;
use App\Support\Securite\VerrouillageConnexion;
use App\Support\Telephone;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Timebox;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;

/**
 * Comptes des clientes de la boutique.
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Les routes sont déclarées dans routes/web.php, sous /fr et /en.
        Fortify::ignoreRoutes();

        $this->app->singleton(LoginRateLimiter::class, VerrouillageConnexion::class);

        // Toutes les redirections restent dans la langue de la cliente.
        $this->app->singleton(LoginResponse::class, fn () => new ReponsesComptes('mon-compte'));
        $this->app->singleton(RegisterResponse::class, fn () => new ReponsesComptes('mon-compte'));
        $this->app->singleton(LogoutResponse::class, fn () => new ReponsesComptes('accueil'));
        $this->app->singleton(PasswordResetResponse::class, fn () => new ReponsesComptes('connexion', 'comptes.mot_de_passe_change'));

        // Même message que l'email existe ou non : on ne révèle pas qui a un compte.
        $this->app->singleton(SuccessfulPasswordResetLinkRequestResponse::class, fn () => new ReponsesComptes(null, 'comptes.lien_envoye'));
        $this->app->singleton(FailedPasswordResetLinkRequestResponse::class, fn () => new ReponsesComptes(null, 'comptes.lien_envoye'));
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreerCliente::class);
        Fortify::resetUserPasswordsUsing(ReinitialiserMotDePasse::class);

        Fortify::loginView(fn () => view('comptes.connexion'));
        Fortify::registerView(fn () => view('comptes.inscription'));
        Fortify::requestPasswordResetLinkView(fn () => view('comptes.mot-de-passe-oublie'));
        Fortify::resetPasswordView(fn (Request $request) => view('comptes.reinitialiser-mot-de-passe', ['request' => $request]));

        // Connexion par email ou par téléphone (D10). La durée de réponse est la
        // même qu'un compte existe ou non.
        Fortify::authenticateUsing(function (Request $request): ?Cliente {
            return (new Timebox)->call(function (Timebox $timebox) use ($request): ?Cliente {
                $cliente = self::trouverCliente((string) $request->input(Fortify::username()));
                $motDePasse = (string) $request->input('password');

                if ($cliente !== null && Hash::check($motDePasse, $cliente->password)) {
                    $timebox->returnEarly();

                    return $cliente;
                }

                return null;
            }, 250_000);
        });

        ResetPassword::createUrlUsing(fn (Cliente $cliente, string $jeton): string => route_langue(
            'reinitialiser-mot-de-passe',
            ['token' => $jeton, 'email' => $cliente->email],
            $cliente->langue,
        ));

        // En plus du verrouillage par compte : 20 envois par minute et par adresse IP.
        RateLimiter::for('comptes', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
    }

    public static function trouverCliente(string $identifiant): ?Cliente
    {
        $identifiant = trim($identifiant);

        if (str_contains($identifiant, '@')) {
            return Cliente::where('email', Str::lower($identifiant))->first();
        }

        $telephone = Telephone::normaliser($identifiant);

        return $telephone === null ? null : Cliente::where('telephone', $telephone)->first();
    }
}
