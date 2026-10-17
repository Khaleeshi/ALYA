<?php

namespace App\Actions\Comptes;

use App\Models\Cliente;
use App\Models\Consentement;
use App\Models\JournalActivite;
use App\Rules\TelephoneValide;
use App\Support\Telephone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

/**
 * Inscription d'une cliente (CDC §12, décision D10) : téléphone obligatoire,
 * email obligatoire pour un compte (il sert à récupérer le mot de passe),
 * accord pour les offres demandé à part et enregistré avec sa date.
 */
class CreerCliente implements CreatesNewUsers
{
    use ReglesMotDePasse;

    /**
     * @param  array<string, mixed>  $donnees
     */
    public function create(array $donnees): Cliente
    {
        $donnees['telephone'] = Telephone::normaliser($donnees['telephone'] ?? null) ?? ($donnees['telephone'] ?? null);

        Validator::make($donnees, [
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'telephone' => ['required', 'string', new TelephoneValide, Rule::unique(Cliente::class, 'telephone')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Cliente::class, 'email')],
            'password' => $this->reglesMotDePasse(),
            'offres' => ['nullable', 'boolean'],
        ])->validate();

        return DB::transaction(function () use ($donnees): Cliente {
            $cliente = Cliente::create([
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'telephone' => $donnees['telephone'],
                'email' => $donnees['email'],
                'password' => $donnees['password'],
                'langue' => app()->getLocale(),
            ]);

            $cliente->enregistrerConsentement(
                Consentement::OFFRES,
                filter_var($donnees['offres'] ?? false, FILTER_VALIDATE_BOOLEAN),
                Consentement::ORIGINE_INSCRIPTION,
                request()->ip(),
            );

            JournalActivite::consigner('cliente.inscription', 'Création du compte cliente', $cliente, auteur: $cliente);

            return $cliente;
        });
    }
}
