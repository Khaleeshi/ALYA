<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Variante;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Fiche produit (cahier §5.3, maquette 2). Un produit en brouillon,
 * archivé, hors de ses dates ou dans une catégorie cachée n'existe pas pour
 * les clientes : page 404.
 */
class ProduitController extends Controller
{
    public function show(Request $request, Produit $produit): View
    {
        $produit->load([
            'categorie',
            'media',
            'variantes' => fn ($requete) => $requete->where('active', true)->with('media'),
            'atouts',
            'ingredients.media',
        ]);

        abort_unless($produit->estVisible() && $produit->variantes->isNotEmpty(), 404);

        // Variante demandée dans l'adresse (?variante=…), sinon la première disponible.
        $choisie = $produit->variantes->firstWhere('id', (int) $request->query('variante'))
            ?? $produit->variantes->first(fn (Variante $variante): bool => $variante->stock > 0)
            ?? $produit->variantes->first();

        $similaires = Produit::visibles()
            ->where('categorie_id', $produit->categorie_id)
            ->whereKeyNot($produit->getKey())
            ->with(['variantes', 'media'])
            ->orderByDesc('publie_le')
            ->limit(4)
            ->get();

        return view('vitrine.boutique.produit', [
            'produit' => $produit,
            'choisie' => $choisie,
            'similaires' => $similaires,
        ]);
    }
}
