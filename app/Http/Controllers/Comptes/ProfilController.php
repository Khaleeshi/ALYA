<?php

namespace App\Http\Controllers\Comptes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Consentement;
use App\Models\JournalActivite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * « Mon profil » (cahier §3, page 21) : prénom, nom, email et accord pour les
 * offres. Le numéro de téléphone, qui sert à se connecter, ne se change pas
 * ici : il faudra le vérifier par un nouveau code (service client en attendant).
 */
class ProfilController extends Controller
{
    public function formulaire(Request $request): View
    {
        /** @var Cliente $cliente */
        $cliente = $request->user('web');

        return view('comptes.profil', [
            'cliente' => $cliente,
            'offres' => $cliente->aConsenti(Consentement::OFFRES),
        ]);
    }

    public function enregistrer(Request $request): RedirectResponse
    {
        /** @var Cliente $cliente */
        $cliente = $request->user('web');

        if (filled($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        }

        $donnees = $request->validate([
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(Cliente::class, 'email')->ignore($cliente)],
            'offres' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($cliente, $donnees, $request): void {
            $cliente->fill([
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'email' => $donnees['email'] ?? null,
            ]);
            $changements = array_keys($cliente->getDirty());
            $cliente->save();

            $offres = $request->boolean('offres');

            if ($offres !== $cliente->aConsenti(Consentement::OFFRES)) {
                $cliente->enregistrerConsentement(Consentement::OFFRES, $offres, Consentement::ORIGINE_PROFIL, $request->ip());
                $changements[] = 'offres';
            }

            if ($changements !== []) {
                JournalActivite::consigner('cliente.profil', 'Modification du profil : '.implode(', ', $changements), $cliente, auteur: $cliente);
            }
        });

        return redirect(route_langue('mon-profil'))->with('statut', __('comptes.profil.enregistre'));
    }
}
