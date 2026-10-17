<?php

namespace App\Filament\Resources\Utilisateurs\Pages;

use App\Filament\Resources\Utilisateurs\UtilisateurResource;
use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Filament\Resources\Pages\CreateRecord;

class CreateUtilisateur extends CreateRecord
{
    protected static string $resource = UtilisateurResource::class;

    protected function afterCreate(): void
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->record;
        $role = $this->data['role'];

        $utilisateur->syncRoles([$role]);
        $utilisateur->syncPermissions($role === Roles::PROPRIETAIRE ? [] : ($this->data['permissions_supplementaires'] ?? []));

        JournalActivite::consigner(
            'personnel.creation',
            sprintf('Création du compte de %s (%s)', $utilisateur->nom_complet, Roles::libelles()[$role]),
            $utilisateur,
            ['role' => $role, 'permissions_supplementaires' => $utilisateur->getDirectPermissions()->pluck('name')->all()],
        );
    }
}
