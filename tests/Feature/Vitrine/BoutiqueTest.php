<?php

use App\Models\Categorie;
use App\Models\Effet;
use App\Models\Gamme;
use App\Models\Ingredient;
use App\Models\Produit;

beforeEach(function () {
    $this->gloss = Categorie::factory()->create(['nom' => ['fr' => 'Gloss'], 'slug' => 'gloss', 'position' => 0]);
    $this->skincare = Categorie::factory()->create(['nom' => ['fr' => 'Skincare'], 'slug' => 'skincare', 'position' => 1]);
});

it('liste les produits en ligne, sans les brouillons', function () {
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Gloss Amani']]);
    Produit::factory()->for($this->gloss)->brouillon()->create(['nom' => ['fr' => 'Gloss Secret']]);

    $page = $this->get('/fr/boutique')
        ->assertOk()
        ->assertSee('Gloss Amani')
        ->assertDontSee('Gloss Secret')
        ->assertSee('1 produit');

    expect($page->getContent())->toMatch('/aria-current="page"\s*>Boutique</');
});

it('annonce les produits à venir tant que le catalogue est vide', function () {
    $this->get('/fr/boutique')
        ->assertOk()
        ->assertSee(__('vitrine.boutique.bientot'));
});

it('cherche sans accents ni majuscules et trie par prix', function () {
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Crème Éclat'], 'prix' => 9000]);
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Crème Douceur'], 'prix' => 4000]);
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Poudre Soleil'], 'prix' => 6000]);

    $this->get('/fr/boutique?q=CREME&tri=prix-croissant')
        ->assertOk()
        ->assertSeeInOrder(['Crème Douceur', 'Crème Éclat'])
        ->assertDontSee('Poudre Soleil')
        ->assertSee('Résultats pour « CREME »');
});

it('filtre par prix, disponibilité, type, effet et ingrédient', function () {
    $hydratation = Effet::factory()->create(['nom' => ['fr' => 'Hydratation'], 'slug' => 'hydratation']);
    $jojoba = Ingredient::factory()->create(['nom' => ['fr' => 'Huile de jojoba'], 'slug' => 'huile-de-jojoba']);

    $baume = Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Baume'], 'prix' => 3500]);
    $baume->effets()->attach($hydratation);
    $baume->ingredients()->attach($jojoba);
    $serum = Produit::factory()->for($this->skincare)->editionLimitee()->create(['nom' => ['fr' => 'Sérum'], 'prix' => 12000]);
    Produit::factory()->for($this->skincare)->avecTeintes(['50 ml' => 0])->create(['nom' => ['fr' => 'Crème épuisée'], 'prix' => 9500]);

    $this->get('/fr/boutique?prix=moins-de-5000')->assertSee('Baume')->assertDontSee('Sérum');
    $this->get('/fr/boutique?dispo=1')->assertSee('Sérum')->assertDontSee('Crème épuisée');
    $this->get('/fr/boutique?type=limitee')->assertSee('Sérum')->assertDontSee('Baume');
    $this->get('/fr/boutique?effet[]=hydratation')->assertSee('Baume')->assertDontSee('Sérum');
    $this->get('/fr/boutique?ingredient[]=huile-de-jojoba')
        ->assertSee('Baume')
        ->assertDontSee('Sérum')
        ->assertSee(__('vitrine.boutique.retirer_filtre', ['filtre' => 'Huile de jojoba']));

    expect($serum->enStock())->toBeTrue();
});

it('ignore les filtres inconnus ou mal formés', function () {
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Gloss Amani']]);

    $this->get('/fr/boutique?tri=nimporte&prix=gratuit&type=x&effet[]=%3Cscript%3E&effet[][]=a&q[]=b')
        ->assertOk()
        ->assertSee('Gloss Amani');
});

it('garde les filtres d’une page à l’autre', function () {
    config(['boutique.catalogue.par_page' => 2]);
    Produit::factory()->count(3)->for($this->gloss)->create();

    $this->get('/fr/boutique?dispo=1')
        ->assertOk()
        ->assertSee('dispo=1&amp;page=2', false);
});

it('affiche une catégorie et refuse une catégorie hors ligne', function () {
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Gloss Amani']]);
    Produit::factory()->for($this->skincare)->create(['nom' => ['fr' => 'Sérum Éclat']]);

    $page = $this->get('/fr/boutique/gloss')
        ->assertOk()
        ->assertSee('Gloss Amani')
        ->assertDontSee('Sérum Éclat');

    // La catégorie est en surbrillance, et « Boutique » reste actif dans le menu.
    expect($page->getContent())
        ->toMatch('/aria-current="page"\s*>Gloss</')
        ->toMatch('/aria-current="page"\s*>Boutique</');

    $this->skincare->update(['en_ligne' => false]);
    $this->get('/fr/boutique/skincare')->assertNotFound();
});

it('affiche la fiche produit avec ses couleurs, sa disponibilité et ses données structurées', function () {
    $produit = Produit::factory()->for($this->gloss)
        ->avecTeintes(['Nude Glow' => 12, 'Chocolate' => 0])
        ->create(['nom' => ['fr' => 'Gloss Amani'], 'prix' => 5000, 'bienfaits' => ['fr' => "Hydrate\nNon collant"]]);

    $this->get('/fr/produit/'.$produit->slug)
        ->assertOk()
        ->assertSee('<h1', false)
        ->assertSee('Gloss Amani')
        ->assertSee('Nude Glow')
        ->assertSee('Chocolate, Épuisé')
        ->assertSee(__('vitrine.fiche.en_stock'))
        ->assertSee('Non collant')
        ->assertSee('"@type":"Product"', false)
        ->assertSee('"priceCurrency":"XOF"', false)
        ->assertSee('<link rel="canonical" href="'.url('/fr/produit/'.$produit->slug).'">', false);
});

it('ouvre la fiche sur la couleur demandée', function () {
    $produit = Produit::factory()->for($this->gloss)->avecTeintes(['Nude Glow' => 12, 'Rose' => 4])->create();
    $rose = $produit->variantes()->where('position', 1)->sole();

    $this->get('/fr/produit/'.$produit->slug.'?variante='.$rose->id)
        ->assertOk()
        ->assertSee('value="'.$rose->id.'" x-model.number="choisie" checked', false);
});

it('ne montre pas un produit en brouillon, archivé, hors de ses dates ou sans variante en vente', function () {
    $brouillon = Produit::factory()->for($this->gloss)->brouillon()->create();
    $archive = Produit::factory()->for($this->gloss)->create(['statut' => Produit::ARCHIVE]);
    $termine = Produit::factory()->for($this->gloss)->create(['campagne_fin_le' => now()->subDay()]);
    $retire = Produit::factory()->for($this->gloss)->create();
    $retire->variantes()->update(['active' => false]);

    foreach ([$brouillon, $archive, $termine, $retire] as $produit) {
        $this->get('/fr/produit/'.$produit->slug)->assertNotFound();
    }
});

it('propose des produits similaires de la même catégorie', function () {
    $produit = Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Gloss Amani']]);
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Baume Douceur']]);
    Produit::factory()->for($this->skincare)->create(['nom' => ['fr' => 'Sérum Éclat']]);

    $this->get('/fr/produit/'.$produit->slug)
        ->assertSee(__('vitrine.fiche.similaires'))
        ->assertSee('Baume Douceur')
        ->assertDontSee('Sérum Éclat');
});

it('présente les collections par type et cache celles qui sont hors ligne', function () {
    Gamme::factory()->create(['nom' => ['fr' => 'Gloss permanents']]);
    Gamme::factory()->editionLimitee()->create(['nom' => ['fr' => 'Rose Élégance']]);
    Gamme::factory()->create(['nom' => ['fr' => 'Collection cachée'], 'en_ligne' => false]);
    Produit::factory()->for($this->gloss)->coffret()->create(['nom' => ['fr' => 'Coffret Lèvres Parfaites']]);

    $this->get('/fr/collections')
        ->assertOk()
        ->assertSee('Collection Gloss')
        ->assertSee('Collections permanentes')
        ->assertSee('Gloss permanents')
        ->assertSee('Rose Élégance')
        ->assertSee('Coffret Lèvres Parfaites')
        ->assertDontSee('Collection cachée');
});

it('affiche les produits d’une collection', function () {
    $collection = Gamme::factory()->create(['nom' => ['fr' => 'Baumes à lèvres']]);
    $collection->produits()->attach(Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Baume Douceur']]));
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Gloss Amani']]);
    $cachee = Gamme::factory()->create(['en_ligne' => false]);

    $this->get('/fr/collections/'.$collection->slug)
        ->assertOk()
        ->assertSee('Baume Douceur')
        ->assertDontSee('Gloss Amani');

    $this->get('/fr/collections/'.$cachee->slug)->assertNotFound();
});

it('rassemble les éditions limitées et les coffrets sur leurs pages', function () {
    Produit::factory()->for($this->gloss)->editionLimitee()->create(['nom' => ['fr' => 'Gloss Golden Glow']]);
    Produit::factory()->for($this->gloss)->coffret()->create(['nom' => ['fr' => 'Coffret Beauté Luxe']]);
    Produit::factory()->for($this->gloss)->create(['nom' => ['fr' => 'Gloss Amani']]);

    $this->get('/fr/editions-limitees')->assertOk()->assertSee('Gloss Golden Glow')->assertDontSee('Gloss Amani');
    $this->get('/fr/coffrets-cadeaux')->assertOk()->assertSee('Coffret Beauté Luxe')->assertSee('Voir le coffret')->assertDontSee('Gloss Amani');
});

it('montre les nouvelles pages dans le menu principal', function () {
    $this->get('/fr')
        ->assertSee('href="'.url('/fr/boutique').'"', false)
        ->assertSee('href="'.url('/fr/collections').'"', false)
        ->assertSee('href="'.url('/fr/editions-limitees').'"', false)
        ->assertSee('href="'.url('/fr/coffrets-cadeaux').'"', false);
});
