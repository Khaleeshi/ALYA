<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Gamme;
use App\Models\Produit;
use Illuminate\Contracts\View\View;

/**
 * Accueil (cahier §5.1, maquette 1) : bannière de marque, réassurance,
 * catégories, nouveautés, collections et coffrets.
 *
 * Les meilleures ventes (calculées sur les ventes réelles) et les
 * promotions arriveront avec les commandes et les promotions : rien n'est
 * affiché avant d'exister.
 */
class AccueilController extends Controller
{
    public function __invoke(): View
    {
        return view('vitrine.accueil', [
            'categories' => Categorie::visibles()->with('media')->get(),
            'nouveautes' => Produit::nouveautes()->with(['variantes', 'media'])->limit(4)->get(),
            'gammes' => Gamme::visibles()->with('media')->limit(3)->get(),
            'coffrets' => Produit::visibles()
                ->where('nature', Produit::NATURE_COFFRET)
                ->with(['variantes', 'media'])
                ->orderByDesc('publie_le')
                ->limit(3)
                ->get(),
        ]);
    }
}
