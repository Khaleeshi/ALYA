<?php

use App\Models\Categorie;
use App\Models\Gamme;
use App\Models\Ingredient;
use App\Models\JournalActivite;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Variante;
use App\Support\Catalogue\Stock;
use App\Support\Catalogue\StockInsuffisant;
use App\Support\Montant;
use Database\Seeders\CatalogueDemonstrationSeeder;
use Database\Seeders\CategoriesSeeder;

it("déduit l'adresse du nom français et la garde unique", function () {
    $premier = Produit::factory()->create(['nom' => ['fr' => 'Gloss Crème Éclat']]);
    $second = Produit::factory()->create(['nom' => ['fr' => 'Gloss Crème Éclat']]);

    expect($premier->slug)->toBe('gloss-creme-eclat')
        ->and($second->slug)->toBe('gloss-creme-eclat-2');
});

it('date la première mise en ligne et ne la change plus', function () {
    $produit = Produit::factory()->brouillon()->create();
    expect($produit->publie_le)->toBeNull();

    $this->freezeSecond();
    $produit->update(['statut' => Produit::PUBLIE]);
    $date = $produit->publie_le;

    $this->travel(2)->days();
    $produit->update(['statut' => Produit::ARCHIVE]);
    $produit->update(['statut' => Produit::PUBLIE]);

    expect($produit->fresh()->publie_le->equalTo($date))->toBeTrue();
});

it("ne montre aux clientes que les produits publiés, dans leurs dates, d'une catégorie visible", function () {
    $visible = Produit::factory()->create();
    Produit::factory()->brouillon()->create();
    Produit::factory()->create(['statut' => Produit::ARCHIVE]);
    Produit::factory()->create(['campagne_debut_le' => now()->addDay()]);
    Produit::factory()->create(['campagne_fin_le' => now()->subDay()]);
    Produit::factory()->for(Categorie::factory()->cachee())->create();

    expect(Produit::visibles()->pluck('id')->all())->toBe([$visible->id]);
});

it('cherche sans tenir compte des accents ni des majuscules, jusque dans les couleurs', function () {
    $categorie = Categorie::factory()->create(['nom' => ['fr' => 'Skincare']]);
    $baume = Produit::factory()->for($categorie)->create(['nom' => ['fr' => 'Baume Éclat'], 'mots_cles' => ['fr' => 'lèvres sèches']]);
    $gloss = Produit::factory()->avecTeintes(['Nude Glow' => 3])->create(['nom' => ['fr' => 'Gloss Amani']]);

    expect(Produit::recherche('baume eclat')->pluck('id')->all())->toBe([$baume->id])
        ->and(Produit::recherche('LEVRES')->pluck('id')->all())->toBe([$baume->id])
        ->and(Produit::recherche('skincare')->pluck('id')->all())->toBe([$baume->id])
        ->and(Produit::recherche('nude')->pluck('id')->all())->toBe([$gloss->id])
        ->and(Produit::recherche('introuvable')->count())->toBe(0);
});

it('ajoute les ingrédients clés à la recherche une fois enregistrés', function () {
    $produit = Produit::factory()->create();
    $produit->ingredients()->attach(Ingredient::factory()->create(['nom' => ['fr' => 'Huile de jojoba']]));
    $produit->actualiserTexteRecherche();

    expect(Produit::recherche('jojoba')->pluck('id')->all())->toBe([$produit->id]);
});

it('note le stock de départ et attribue une référence', function () {
    $variante = Produit::factory()->avecTeintes(['Nude Glow' => 12])->create()->variantes->sole();

    expect($variante->reference)->toStartWith('AMN-')
        ->and($variante->mouvements()->sole())
        ->quantite->toBe(12)
        ->stock_apres->toBe(12)
        ->motif->toBe(MouvementStock::STOCK_INITIAL);
});

it('change le stock par un mouvement, au journal, sans jamais passer sous zéro', function () {
    $this->actingAs(administrateur(), 'personnel');
    $variante = Produit::factory()->create()->variantes->sole();

    Stock::ajuster($variante, 5, MouvementStock::REAPPROVISIONNEMENT, 'Arrivage');
    Stock::ajuster($variante, -3, MouvementStock::CORRECTION, 'Inventaire');

    expect($variante->stock)->toBe(12)
        ->and($variante->fresh()->stock)->toBe(12)
        ->and($variante->mouvements()->count())->toBe(3)
        ->and(JournalActivite::where('action', 'catalogue.stock')->count())->toBe(2);

    expect(fn () => Stock::ajuster($variante, -13, MouvementStock::CORRECTION))->toThrow(StockInsuffisant::class)
        ->and($variante->fresh()->stock)->toBe(12);
});

it('refuse de changer le stock sans mouvement', function () {
    $variante = Produit::factory()->create()->variantes->sole();

    expect(fn () => $variante->update(['stock' => 99]))->toThrow(LogicException::class)
        ->and($variante->fresh()->stock)->toBe(10);
});

it("refuse qu'on modifie ou supprime un mouvement de stock", function () {
    $mouvement = Produit::factory()->create()->variantes->sole()->mouvements()->sole();

    expect(fn () => $mouvement->update(['quantite' => 1]))->toThrow(LogicException::class)
        ->and(fn () => $mouvement->delete())->toThrow(LogicException::class);
});

it('note au journal les changements de prix et de mise en ligne', function () {
    $this->actingAs(administrateur(), 'personnel');
    $produit = Produit::factory()->create(['prix' => 5000]);

    $produit->update(['prix' => 5500]);
    $produit->update(['statut' => Produit::ARCHIVE]);
    $produit->variantes->sole()->update(['prix' => 6000]);

    expect(JournalActivite::where('action', 'catalogue.prix')->pluck('description')->all())
        ->toContain('Prix de « '.$produit->nom.' » : '.Montant::fcfa(5000).' → '.Montant::fcfa(5500))
        ->toHaveCount(2)
        ->and(JournalActivite::where('action', 'catalogue.statut')->sole()->description)->toContain('Publié → Archivé');
});

it('calcule le prix « à partir de », le stock et son état', function () {
    $produit = Produit::factory()->avecTeintes(['Nude' => 0, 'Rose' => 2, 'Prune' => 20])->create(['prix' => 5000, 'seuil_alerte' => 5]);
    [$nude, $rose, $prune] = $produit->variantes;
    $prune->update(['prix' => 4500]);

    $produit->load('variantes');

    expect($produit->prixMinimum())->toBe(4500)
        ->and($produit->aPlusieursPrix())->toBeTrue()
        ->and($produit->enStock())->toBeTrue()
        ->and($nude->etatStock())->toBe(Variante::RUPTURE)
        ->and($rose->etatStock())->toBe(Variante::STOCK_LIMITE)
        ->and($prune->etatStock())->toBe(Variante::EN_STOCK);
});

it('cache les collections hors de leurs dates', function () {
    $visible = Gamme::factory()->create();
    Gamme::factory()->create(['fin_le' => now()->subHour()]);
    Gamme::factory()->create(['en_ligne' => false]);

    expect(Gamme::visibles()->pluck('id')->all())->toBe([$visible->id]);
});

it('crée les trois catégories du cahier sans toucher à celles qui existent', function () {
    Categorie::factory()->create(['slug' => 'gloss', 'nom' => ['fr' => 'Gloss & Lip Oil']]);

    $this->seed(CategoriesSeeder::class);
    $this->seed(CategoriesSeeder::class);

    expect(Categorie::orderBy('position')->pluck('slug')->all())->toEqualCanonicalizing(['gloss', 'skincare', 'maquillage'])
        ->and(Categorie::where('slug', 'gloss')->sole()->nom)->toBe('Gloss & Lip Oil');
});

it("remplit un catalogue d'essai hors production, jamais en production", function () {
    $this->seed(CatalogueDemonstrationSeeder::class);
    expect(Produit::visibles()->count())->toBe(10);

    app()->detectEnvironment(fn () => 'production');
    expect(fn () => app(CatalogueDemonstrationSeeder::class)->run())->toThrow(RuntimeException::class);
});
