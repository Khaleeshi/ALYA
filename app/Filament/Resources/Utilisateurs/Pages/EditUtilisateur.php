<?php

namespace App\Filament\Resources\Utilisateurs\Pages;

use App\Filament\Resources\Utilisateurs\UtilisateurResource;
use App\Models\JournalActivite;
use App\Models\Utilisateur;
use App\Support\Acces\Roles;
use Filament\Resources\Pages\EditRecord;

class EditUtilisateur extends EditRecord
{
    protected static string $resource = UtilisateurResource::class;

    /** @var array<string, mixed> */
    private array $avant = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->record;

        $data['role'] = $utilisateur->getRoleNames()->first();
        $data['permissions_supplementaires'] = $utilisateur->getDirectPermissions()->pluck('name')->all();

        return $data;
    }

    protected function beforeSave(): void
    {
        $this->avant = $this->etat();
    }

    protected function afterSave(): void
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->record;

        // On ne change ni son propre rôle ni ses propres accès (le champ est verrouillé).
        if (! $utilisateur->is(auth('personnel')->user())) {
            $role = $this->data['role'];
            $utilisateur->syncRoles([$role]);
            $utilisateur->syncPermissions($role === Roles::PROPRIETAIRE ? [] : ($this->data['permissions_supplementaires'] ?? []));
        }

        $apres = $this->etat();
        $changements = array_keys(array_filter($apres, fn ($valeur, $cle) => $valeur !== ($this->avant[$cle] ?? null), ARRAY_FILTER_USE_BOTH));

        if (filled($this->data['password'] ?? null)) {
            $changements[] = 'mot de passe';
        }

        if ($changements !== []) {
            JournalActivite::consigner(
                'personnel.modification',
                sprintf('Modification du compte de %s : %s', $utilisateur->nom_complet, implode(', ', $changements)),
                $utilisateur,
                ['avant' => $this->avant, 'apres' => $apres],
            );
        }
    }

    /**
     * Ce qui compte pour le journal (jamais le mot de passe).
     *
     * @return array<string, mixed>
     */
    private function etat(): array
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->record->fresh();
        $utilisateur->load('roles', 'permissions');

        return [
            'prénom' => $utilisateur->prenom,
            'nom' => $utilisateur->nom,
            'email' => $utilisateur->email,
            'téléphone' => $utilisateur->telephone,
            'actif' => $utilisateur->actif,
            'rôle' => $utilisateur->getRoleNames()->first(),
            'permissions' => $utilisateur->getDirectPermissions()->pluck('name')->sort()->values()->all(),
        ];
    }
}
