<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\LignePanier;
use App\Models\Produit;
use App\Models\Variante;
use App\Support\Panier\ArticleIndisponible;
use App\Support\Panier\GestionPanier;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as CollectionEloquent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Panier (cahier §6.1, maquette 4) : ajouter depuis la fiche ou une
 * vignette, changer une quantité, retirer, vider. Chaque action revient à
 * la page d'origine ; après un ajout, le tiroir du panier s'ouvre.
 */
class PanierController extends Controller
{
    public function __construct(private readonly GestionPanier $panier) {}

    public function index(): View
    {
        $panier = $this->panier->actuel();
        $lignes = $panier?->lignesCompletes() ?? collect();
        $ajustes = $panier ? $this->panier->verifier($lignes) : [];
        $disponibles = $lignes->filter(fn (LignePanier $ligne): bool => $ligne->estDisponible());

        return view('vitrine.panier', [
            'lignes' => $lignes,
            'ajustes' => $ajustes,
            'nombre' => $disponibles->sum('quantite'),
            'sousTotal' => $disponibles->sum(fn (LignePanier $ligne): int => $ligne->total()),
            'suggestions' => $this->suggestions($lignes),
        ]);
    }

    public function ajouter(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'variante' => ['required', 'integer'],
            'quantite' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $variante = Variante::query()->find($donnees['variante']);

        if ($variante === null) {
            return back()->with('panier.erreur', __('vitrine.panier.introuvable'));
        }

        try {
            [, $limitee] = $this->panier->ajouter($variante, (int) ($donnees['quantite'] ?? 1));
        } catch (ArticleIndisponible) {
            return back()->with('panier.erreur', __('vitrine.panier.indisponible', ['nom' => $variante->libelle()]));
        }

        if ($request->boolean('acheter')) {
            return to_route(app()->getLocale().'.panier');
        }

        return back()
            ->with('panier.ouvrir', true)
            ->with('panier.message', $limitee
                ? __('vitrine.panier.limite', ['nom' => $variante->libelle(), 'quantite' => GestionPanier::quantiteMaximale($variante)])
                : __('vitrine.panier.ajoute', ['nom' => $variante->libelle()]));
    }

    public function modifier(Request $request, int $ligne): RedirectResponse
    {
        $donnees = $request->validate(['quantite' => ['required', 'integer', 'min:0', 'max:99']]);
        $ligne = $this->panier->ligne($ligne);

        if ((int) $donnees['quantite'] === 0) {
            $this->panier->retirer($ligne);

            return back()->with('panier.message', __('vitrine.panier.retire', ['nom' => $ligne->variante->libelle()]));
        }

        $limitee = $this->panier->modifier($ligne, (int) $donnees['quantite']);

        return back()->with('panier.message', $limitee
            ? __('vitrine.panier.limite', ['nom' => $ligne->variante->libelle(), 'quantite' => $ligne->quantite])
            : null);
    }

    public function retirer(int $ligne): RedirectResponse
    {
        $ligne = $this->panier->ligne($ligne);
        $this->panier->retirer($ligne);

        return back()->with('panier.message', __('vitrine.panier.retire', ['nom' => $ligne->variante->libelle()]));
    }

    public function vider(): RedirectResponse
    {
        $this->panier->vider();

        return to_route(app()->getLocale().'.panier')->with('panier.message', __('vitrine.panier.vide_ok'));
    }

    /**
     * « Vous aimerez aussi » : des produits en stock des mêmes catégories,
     * qui ne sont pas déjà dans le panier ; à défaut, les nouveautés.
     *
     * @param  Collection<int, LignePanier>  $lignes
     * @return CollectionEloquent<int, Produit>
     */
    private function suggestions(Collection $lignes): CollectionEloquent
    {
        $dejaLa = $lignes->map(fn (LignePanier $ligne): int => $ligne->variante->produit_id)->unique()->all();
        $categories = $lignes->map(fn (LignePanier $ligne): int => $ligne->variante->produit->categorie_id)->unique()->all();

        return Produit::nouveautes()
            ->whereKeyNot($dejaLa)
            ->whereHas('variantes', fn (Builder $q) => $q->where('active', true)->where('stock', '>', 0))
            // Les mêmes catégories d'abord (avant l'ordre des nouveautés).
            ->when($categories !== [], fn (Builder $q) => $q->reorder()
                ->orderByRaw('case when categorie_id in ('.implode(',', array_fill(0, count($categories), '?')).') then 0 else 1 end', $categories)
                ->orderByDesc('publie_le'))
            ->with(['variantes', 'media'])
            ->limit(4)
            ->get();
    }
}
