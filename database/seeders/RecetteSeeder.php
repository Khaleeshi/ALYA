<?php

namespace Database\Seeders;

use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Remplit la version d'essai en ligne (recette, par exemple sur Vercel) à
 * chaque mise en ligne : rôles, catégories, catalogue d'essai, et le compte
 * de la propriétaire si RECETTE_PROPRIETAIRE_EMAIL et
 * RECETTE_PROPRIETAIRE_MOT_DE_PASSE sont réglés. Peut être relancé : rien
 * n'est créé deux fois. Refusé en production.
 */
class RecetteSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Les données de recette ne vont jamais en production.');
        }

        $this->call([
            RolesEtPermissionsSeeder::class,
            CategoriesSeeder::class,
            CatalogueDemonstrationSeeder::class,
        ]);

        $this->proprietaire();
    }

    private function proprietaire(): void
    {
        ['email' => $email, 'mot_de_passe' => $motDePasse, 'prenom' => $prenom] = config('boutique.recette.proprietaire');

        if (blank($email) || blank($motDePasse)) {
            $this->command?->warn('Compte de la propriétaire non créé : RECETTE_PROPRIETAIRE_EMAIL et RECETTE_PROPRIETAIRE_MOT_DE_PASSE ne sont pas réglés.');

            return;
        }

        $email = mb_strtolower(trim($email));

        if (Utilisateur::query()->where('email', $email)->exists()) {
            return;
        }

        $utilisateur = Utilisateur::create([
            'prenom' => $prenom,
            'nom' => 'AMANI',
            'email' => $email,
            'password' => $motDePasse,
            'actif' => true,
        ]);
        $utilisateur->assignRole(Roles::PROPRIETAIRE);

        JournalActivite::consigner('personnel.creation', sprintf('Création du compte de %s (recette)', $utilisateur->nom_complet), $utilisateur);
    }
}
