<?php

namespace App\Http\Controllers\Comptes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Consentement;
use App\Models\JournalActivite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Création du compte, une fois le numéro vérifié par le code SMS : prénom,
 * nom, email facultatif et accord pour les offres (cahier §6.2 et §15).
 */
class InscriptionController extends Controller
{
    public const SESSION_TELEPHONE_VERIFIE = 'inscription.telephone_verifie';

    /** Délai pour compléter le compte après la vérification du numéro. */
    public const VALIDITE_MINUTES = 30;

    public static function retenirTelephoneVerifie(Request $request, string $telephone): void
    {
        $request->session()->put(self::SESSION_TELEPHONE_VERIFIE, [
            'telephone' => $telephone,
            'le' => now()->getTimestamp(),
            'souvenir' => $request->boolean('souvenir'),
        ]);
    }

    public function formulaire(Request $request): View|RedirectResponse
    {
        $verification = $this->verification($request);

        if ($verification === null) {
            return redirect(route_langue('connexion'));
        }

        return view('comptes.inscription', ['telephone' => $verification['telephone']]);
    }

    public function enregistrer(Request $request): RedirectResponse
    {
        $verification = $this->verification($request);

        if ($verification === null) {
            return redirect(route_langue('connexion'));
        }

        if (filled($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        }

        $donnees = $request->validate([
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(Cliente::class, 'email')],
            'offres' => ['nullable', 'boolean'],
        ]);

        $cliente = DB::transaction(function () use ($donnees, $verification, $request): Cliente {
            // Le compte a pu être créé entre-temps depuis un autre onglet.
            $existante = Cliente::where('telephone', $verification['telephone'])->lockForUpdate()->first();

            if ($existante !== null) {
                return $existante;
            }

            $cliente = new Cliente([
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'telephone' => $verification['telephone'],
                'email' => $donnees['email'] ?? null,
                'langue' => app()->getLocale(),
            ]);
            $cliente->telephone_verifie_le = now();
            $cliente->save();

            $cliente->enregistrerConsentement(
                Consentement::OFFRES,
                $request->boolean('offres'),
                Consentement::ORIGINE_INSCRIPTION,
                $request->ip(),
            );

            JournalActivite::consigner('cliente.inscription', 'Création du compte cliente', $cliente, auteur: $cliente);

            return $cliente;
        });

        $request->session()->forget(self::SESSION_TELEPHONE_VERIFIE);

        Auth::guard('web')->login($cliente, (bool) $verification['souvenir']);
        $request->session()->regenerate();

        return redirect()->intended(route_langue('mon-compte'));
    }

    /**
     * @return ?array{telephone: string, le: int, souvenir: bool}
     */
    private function verification(Request $request): ?array
    {
        $verification = $request->session()->get(self::SESSION_TELEPHONE_VERIFIE);

        if (! is_array($verification) || now()->getTimestamp() - $verification['le'] > self::VALIDITE_MINUTES * 60) {
            return null;
        }

        return $verification;
    }
}
