<?php

namespace App\Console\Commands;

use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Database\Seeders\RolesEtPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Crée un compte du personnel depuis le serveur. Sert surtout au tout premier
 * compte (la propriétaire) : les suivants se créent dans l'administration.
 * Le mot de passe est saisi à la main, jamais écrit dans le code.
 */
class CreerPersonnel extends Command
{
    protected $signature = 'amani:creer-personnel';

    protected $description = 'Crée un compte du personnel (propriétaire, administrateur ou livreur)';

    public function handle(): int
    {
        $this->callSilently('db:seed', ['--class' => RolesEtPermissionsSeeder::class, '--force' => true]);

        $donnees = [
            'prenom' => text('Prénom', required: true),
            'nom' => text('Nom', required: true),
            'email' => text('Email', required: true),
            'role' => select('Rôle', Roles::libelles(), default: Roles::PROPRIETAIRE),
            'password' => password('Mot de passe (12 caractères au moins, lettres et chiffres)', required: true),
        ];

        $validation = Validator::make($donnees, [
            'email' => ['email', 'unique:utilisateurs,email'],
            'password' => [Password::min(12)->letters()->numbers()],
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $erreur) {
                $this->components->error($erreur);
            }

            return self::FAILURE;
        }

        $utilisateur = Utilisateur::create([
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'email' => $donnees['email'],
            'password' => $donnees['password'],
            'actif' => true,
        ]);

        $utilisateur->assignRole($donnees['role']);

        JournalActivite::consigner(
            'personnel.creation',
            sprintf('Création du compte de %s (%s) depuis le serveur', $utilisateur->nom_complet, Roles::libelles()[$donnees['role']]),
            $utilisateur,
        );

        $this->components->info('Compte créé. À la première connexion sur /administration, la double authentification sera demandée.');

        return self::SUCCESS;
    }
}
