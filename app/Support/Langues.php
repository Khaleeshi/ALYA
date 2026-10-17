<?php

namespace App\Support;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Le site existe en français (par défaut) et en anglais, chacun sous son
 * préfixe : /fr/connexion, /en/login. Les morceaux d'adresse sont traduits
 * dans lang/{fr,en}/chemins.php ; les routes portent le nom « fr.connexion »,
 * « en.connexion », etc.
 */
final class Langues
{
    public const DISPONIBLES = ['fr', 'en'];

    public const PAR_DEFAUT = 'fr';

    public static function estDisponible(?string $langue): bool
    {
        return in_array($langue, self::DISPONIBLES, true);
    }

    /**
     * Langue lue dans l'adresse demandée (/en/… donne « en »), sinon la langue par défaut.
     */
    public static function depuisRequete(Request $request): string
    {
        $segment = $request->segment(1);

        return self::estDisponible($segment) ? $segment : self::PAR_DEFAUT;
    }

    /**
     * Déclare un groupe de routes une fois par langue.
     *
     * @param  Closure(string): void  $definir  reçoit la langue du groupe
     */
    public static function routes(Closure $definir): void
    {
        foreach (self::DISPONIBLES as $langue) {
            Route::prefix($langue)
                ->name($langue.'.')
                ->middleware('langue:'.$langue)
                ->group(fn () => $definir($langue));
        }
    }

    /**
     * Morceau d'adresse traduit : chemin('connexion', 'en') donne « login ».
     */
    public static function chemin(string $cle, string $langue): string
    {
        return trans('chemins.'.$cle, [], $langue);
    }

    /**
     * Adresse d'une route dans la langue demandée (la langue courante par défaut).
     */
    public static function route(string $nom, array $parametres = [], ?string $langue = null, bool $absolue = true): string
    {
        $langue = self::estDisponible($langue) ? $langue : app()->getLocale();

        return route($langue.'.'.$nom, $parametres, $absolue);
    }

    /**
     * Adresse de la page affichée, dans l'autre langue. Sert au sélecteur
     * FR / EN et, plus tard, aux balises hreflang.
     */
    public static function pageEquivalente(string $langue): string
    {
        $route = request()->route();
        $nom = $route?->getName();

        if ($nom !== null && preg_match('/^(?:fr|en)\.(.+)$/', $nom, $morceaux) && Route::has($langue.'.'.$morceaux[1])) {
            return route($langue.'.'.$morceaux[1], $route->parameters());
        }

        return route($langue.'.accueil');
    }
}
