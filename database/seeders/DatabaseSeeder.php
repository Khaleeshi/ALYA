<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Aucun compte n'est créé ici : le premier compte de la propriétaire se crée
     * avec « php artisan amani:creer-personnel », mot de passe saisi à la main.
     */
    public function run(): void
    {
        $this->call(RolesEtPermissionsSeeder::class);
    }
}
