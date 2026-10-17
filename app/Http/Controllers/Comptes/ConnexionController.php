<?php

namespace App\Http\Controllers\Comptes;

use App\Http\Controllers\Controller;
use App\Listeners\JournaliserAuthentification;
use App\Models\Cliente;
use App\Rules\TelephoneSenegalais;
use App\Support\Comptes\CodesConnexion;
use App\Support\Comptes\EnvoiRefuse;
use App\Support\Securite\VerrouillageConnexion;
use App\Support\Telephone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Connexion des clientes par téléphone et code SMS (cahier §6.2).
 *
 * 1. La cliente donne son numéro : un code part par SMS.
 * 2. Elle saisit le code : si un compte existe, elle est connectée ; sinon,
 *    son numéro est vérifié et elle complète son compte (InscriptionController).
 */
class ConnexionController extends Controller
{
    public const SESSION_TELEPHONE = 'connexion.telephone';

    public function formulaire(): View
    {
        return view('comptes.connexion');
    }

    public function envoyerCode(Request $request, CodesConnexion $codes): RedirectResponse
    {
        $donnees = $request->validate([
            'telephone' => ['required', 'string', new TelephoneSenegalais],
        ]);

        $telephone = Telephone::normaliser($donnees['telephone']);

        try {
            $codes->envoyer($telephone, $request->ip());
        } catch (EnvoiRefuse $refus) {
            return back()->withInput()->withErrors(['telephone' => $refus->getMessage()]);
        }

        $request->session()->put(self::SESSION_TELEPHONE, $telephone);

        return redirect(route_langue('connexion.code'))
            ->with('statut', __('comptes.code.envoye', ['telephone' => Telephone::afficher($telephone)]));
    }

    public function formulaireCode(Request $request): View|RedirectResponse
    {
        $telephone = $request->session()->get(self::SESSION_TELEPHONE);

        if ($telephone === null) {
            return redirect(route_langue('connexion'));
        }

        return view('comptes.code', ['telephone' => $telephone]);
    }

    public function verifierCode(Request $request, CodesConnexion $codes): RedirectResponse
    {
        $telephone = $request->session()->get(self::SESSION_TELEPHONE);

        if ($telephone === null) {
            return redirect(route_langue('connexion'));
        }

        $request->validate(['code' => ['required', 'string', 'max:20']]);

        $cle = VerrouillageConnexion::cle('boutique', $telephone, $request->ip());

        if (VerrouillageConnexion::estVerrouille($cle)) {
            return back()->withErrors(['code' => VerrouillageConnexion::message($cle)]);
        }

        if (! $codes->verifier($telephone, preg_replace('/\D/', '', (string) $request->input('code')))) {
            if (VerrouillageConnexion::echec($cle)) {
                JournaliserAuthentification::consignerVerrouillage('boutique', $telephone);

                return back()->withErrors(['code' => VerrouillageConnexion::message($cle)]);
            }

            return back()->withErrors(['code' => __('comptes.code.invalide')]);
        }

        VerrouillageConnexion::reussite($cle);
        $request->session()->forget(self::SESSION_TELEPHONE);

        $cliente = Cliente::firstWhere('telephone', $telephone);

        if ($cliente === null) {
            InscriptionController::retenirTelephoneVerifie($request, $telephone);

            return redirect(route_langue('inscription'));
        }

        Auth::guard('web')->login($cliente, $request->boolean('souvenir'));
        $request->session()->regenerate();

        return redirect()->intended(route_langue('mon-compte'));
    }

    public function renvoyerCode(Request $request, CodesConnexion $codes): RedirectResponse
    {
        $telephone = $request->session()->get(self::SESSION_TELEPHONE);

        if ($telephone === null) {
            return redirect(route_langue('connexion'));
        }

        try {
            $codes->envoyer($telephone, $request->ip());
        } catch (EnvoiRefuse $refus) {
            return back()->withErrors(['code' => $refus->getMessage()]);
        }

        return back()->with('statut', __('comptes.code.envoye', ['telephone' => Telephone::afficher($telephone)]));
    }

    public function deconnexion(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route_langue('accueil'));
    }
}
