<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Gamme;
use App\Models\Produit;
use App\Support\Catalogue\FiltresBoutique;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Pages de liste de la vitrine (cahier §3 et §5.2) : la boutique, une
 * catégorie, les éditions limitées, les coffrets cadeaux. Même grille,
 * mêmes filtres ; seuls l'en-tête et les produits de départ changent.
 */
class BoutiqueController extends Controller
{
    public function index(Request $request): View
    {
        return $this->liste($request, Produit::visibles(), [
            'titre' => __('vitrine.boutique.titre'),
            'sousTitre' => __('vitrine.boutique.sous_titre'),
            'categorieActive' => null,
        ]);
    }

    public function categorie(Request $request, Categorie $categorie): View
    {
        abort_unless($categorie->en_ligne, 404);

        return $this->liste($request, Produit::visibles()->where('categorie_id', $categorie->id), [
            'titre' => $categorie->nom,
            'sousTitre' => $categorie->description,
            'photo' => $categorie->getFirstMedia(Categorie::COUVERTURE),
            'categorieActive' => $categorie,
        ]);
    }

    public function editionsLimitees(Request $request): View
    {
        return $this->liste($request, Produit::visibles()->where('edition_limitee', true), [
            'titre' => __('vitrine.editions.titre'),
            'sousTitre' => __('vitrine.editions.sous_titre'),
            'gammes' => Gamme::visibles()->where('type', Gamme::EDITION_LIMITEE)->with('media')->get(),
            'titreGrille' => __('vitrine.editions.produits'),
        ]);
    }

    public function coffrets(Request $request): View
    {
        return $this->liste($request, Produit::visibles()->where('nature', Produit::NATURE_COFFRET), [
            'titre' => __('vitrine.coffrets.titre'),
            'sousTitre' => __('vitrine.coffrets.sous_titre'),
        ]);
    }

    /**
     * @param  Builder<Produit>  $base
     * @param  array<string, mixed>  $page
     */
    private function liste(Request $request, Builder $base, array $page): View
    {
        $filtres = FiltresBoutique::depuis($request);
        $options = FiltresBoutique::options($base);

        $produits = $filtres->appliquer(clone $base)
            ->with(['variantes', 'media'])
            ->paginate((int) config('boutique.catalogue.par_page'))
            ->withQueryString();

        return view('vitrine.boutique.liste', [
            'photo' => null,
            'gammes' => collect(),
            'titreGrille' => null,
            'categories' => array_key_exists('categorieActive', $page) ? Categorie::visibles()->get() : collect(),
            ...$page,
            'produits' => $produits,
            'filtres' => $filtres,
            'options' => $options,
            'puces' => $filtres->puces($request, $options),
            'catalogueVide' => ! $base->exists(),
        ]);
    }
}
