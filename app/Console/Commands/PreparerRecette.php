<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Prépare la version d'essai en ligne (recette, sur Vercel) à chaque mise
 * en ligne : vérifie les réglages, met la base à jour, la remplit
 * (RecetteSeeder). Explique en clair ce qui manque. Refusé en production.
 */
class PreparerRecette extends Command
{
    protected $signature = 'amani:preparer-recette';

    protected $description = 'Prépare la version d’essai en ligne : base à jour, catalogue d’essai, compte de la propriétaire';

    public function handle(): int
    {
        if ($this->laravel->isProduction()) {
            $this->components->error('Refusé : cette commande ne sert qu’à la version d’essai (APP_ENV=recette).');

            return self::FAILURE;
        }

        if (blank(config('app.key'))) {
            $this->components->error('APP_KEY manque : ajoutez-la dans Vercel → Settings → Environment Variables, puis relancez la mise en ligne.');

            return self::FAILURE;
        }

        try {
            DB::connection()->getPdo();
        } catch (Throwable $erreur) {
            $this->components->error('La base de données ne répond pas. Dans Vercel → Storage, créez une base Neon et reliez-la au projet, puis relancez la mise en ligne.');
            $this->line('Détail : '.$erreur->getMessage());

            return self::FAILURE;
        }

        $this->call('migrate', ['--force' => true]);
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\RecetteSeeder', '--force' => true]);

        $this->components->info('Version d’essai prête.');

        return self::SUCCESS;
    }
}
