<?php

namespace App\Providers;

use App\Listeners\JournaliserAuthentification;
use App\Models\Categorie;
use App\Models\Cliente;
use App\Models\Gamme;
use App\Models\Ingredient;
use App\Models\Produit;
use App\Models\Utilisateur;
use App\Models\Variante;
use App\Support\Acces\Permissions;
use App\Support\Langues;
use App\Support\Medias\DeclinaisonsImages;
use App\Support\Sms\EnvoiSms;
use App\Support\Sms\SmsJournal;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Prestataire SMS des codes de connexion (config/services.php).
        $this->app->singleton(EnvoiSms::class, fn (): EnvoiSms => match (config('services.sms.pilote')) {
            'journal' => new SmsJournal,
            default => throw new InvalidArgumentException('Pilote SMS inconnu : '.config('services.sms.pilote')),
        });
    }

    public function boot(): void
    {
        // Noms courts dans les colonnes polymorphes (journal, rôles, médias).
        Relation::morphMap([
            'cliente' => Cliente::class,
            'utilisateur' => Utilisateur::class,
            'categorie' => Categorie::class,
            'gamme' => Gamme::class,
            'produit' => Produit::class,
            'variante' => Variante::class,
            'ingredient' => Ingredient::class,
        ]);

        // La propriétaire a toutes les permissions du personnel, y compris celles
        // ajoutées après la création de son compte. Les règles « jamais »
        // (supprimer une ligne du journal…) ne sont pas des permissions et
        // restent refusées, même pour elle.
        Gate::before(function (mixed $personne, string $capacite): ?bool {
            if ($personne instanceof Utilisateur
                && $personne->actif
                && in_array($capacite, Permissions::toutes(), true)
                && $personne->estProprietaire()) {
                return true;
            }

            return null;
        });

        // En plus des limites propres aux codes SMS : 20 envois de formulaire
        // de compte par minute et par adresse IP.
        RateLimiter::for('comptes', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));

        // Contenus traduisibles : un champ anglais vide retombe sur le français.
        Translatable::fallback(fallbackLocale: Langues::PAR_DEFAUT);

        Event::subscribe(JournaliserAuthentification::class);
        Event::listen(MediaHasBeenAddedEvent::class, [DeclinaisonsImages::class, 'lireDimensions']);

        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
