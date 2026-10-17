<?php

namespace App\Providers;

use App\Listeners\JournaliserAuthentification;
use App\Models\Cliente;
use App\Models\Utilisateur;
use App\Support\Acces\Permissions;
use App\Support\Langues;
use App\Support\Medias\DeclinaisonsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Noms courts dans les colonnes polymorphes (journal, rôles, médias).
        Relation::morphMap([
            'cliente' => Cliente::class,
            'utilisateur' => Utilisateur::class,
        ]);

        // La dirigeante a toutes les permissions du personnel, y compris celles
        // ajoutées après la création de son compte. Les règles « jamais »
        // (supprimer une ligne du journal…) ne sont pas des permissions et
        // restent refusées, même pour elle.
        Gate::before(function (mixed $personne, string $capacite): ?bool {
            if ($personne instanceof Utilisateur
                && $personne->actif
                && in_array($capacite, Permissions::toutes(), true)
                && $personne->estDirigeante()) {
                return true;
            }

            return null;
        });

        // Contenus traduisibles : un champ anglais vide retombe sur le français (audit 4.6).
        Translatable::fallback(fallbackLocale: Langues::PAR_DEFAUT);

        Event::subscribe(JournaliserAuthentification::class);
        Event::listen(MediaHasBeenAddedEvent::class, [DeclinaisonsImages::class, 'lireDimensions']);

        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
