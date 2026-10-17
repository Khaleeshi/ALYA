<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Gamme;
use App\Models\Produit;
use Illuminate\Contracts\View\View;

/**
 * Collections (cahier §3, maquette 3) : les catégories, les collections
 * permanentes et saisonnières, les éditions limitées, les coffrets.
 * Une section sans contenu ne s'affiche pas.
 */
class CollectionsController extends Controller
{
    public function index(): View
    {
        $gammes = Gamme::visibles()->with('media')->get()->groupBy('type');

        return view('vitrine.collections.index', [
            'categories' => Categorie::visibles()->with('media')->get(),
            'permanentes' => $gammes->get(Gamme::PERMANENTE, collect()),
            'saisonnieres' => $gammes->get(Gamme::SAISONNIERE, collect()),
            'limitees' => $gammes->get(Gamme::EDITION_LIMITEE, collect()),
            'coffrets' => Produit::visibles()->where('nature', Produit::NATURE_COFFRET)->with(['variantes', 'media'])->orderByDesc('publie_le')->limit(3)->get(),
        ]);
    }

    public function show(Gamme $gamme): View
    {
        abort_unless($gamme->estVisible(), 404);

        return view('vitrine.collections.show', [
            'gamme' => $gamme->load('media'),
            'produits' => $gamme->produits()->visibles()->with(['variantes', 'media'])->get(),
        ]);
    }
}
