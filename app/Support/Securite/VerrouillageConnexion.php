<?php

namespace App\Support\Securite;

use App\Support\Telephone;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Verrouillage après cinq échecs de connexion (cahier AMANI §15).
 *
 * Cinq codes faux (boutique) ou cinq mots de passe faux (administration) pour
 * le même identifiant depuis la même adresse bloquent la connexion pendant
 * quinze minutes, même avec le bon code ensuite. Une connexion réussie remet
 * le compteur à zéro.
 */
final class VerrouillageConnexion
{
    public const ESSAIS = 5;

    public const DUREE_SECONDES = 15 * 60;

    /**
     * Clé du compteur pour un espace (« boutique » ou « administration »).
     * Un même téléphone saisi de deux façons donne la même clé.
     */
    public static function cle(string $espace, string $identifiant, ?string $ip): string
    {
        $identifiant = trim($identifiant);

        if (! str_contains($identifiant, '@')) {
            $identifiant = Telephone::normaliser($identifiant) ?? $identifiant;
        }

        return 'connexion|'.$espace.'|'.Str::transliterate(Str::lower($identifiant)).'|'.$ip;
    }

    public static function estVerrouille(string $cle): bool
    {
        return RateLimiter::tooManyAttempts($cle, self::ESSAIS);
    }

    public static function secondesRestantes(string $cle): int
    {
        return RateLimiter::availableIn($cle);
    }

    /**
     * Compte un échec. Renvoie true si cet échec vient de verrouiller la connexion.
     */
    public static function echec(string $cle): bool
    {
        RateLimiter::hit($cle, self::DUREE_SECONDES);

        return self::estVerrouille($cle);
    }

    public static function reussite(string $cle): void
    {
        RateLimiter::clear($cle);
    }

    /**
     * Message à afficher pendant le verrouillage.
     */
    public static function message(string $cle): string
    {
        $secondes = self::secondesRestantes($cle);

        return __('auth.throttle', ['seconds' => $secondes, 'minutes' => (int) ceil($secondes / 60)]);
    }
}
