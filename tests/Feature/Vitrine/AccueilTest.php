<?php

use App\Models\Categorie;
use App\Models\Gamme;
use App\Models\Produit;

it('présente les catégories, les nouveautés, les collections et les coffrets', function () {
    $gloss = Categorie::factory()->create(['nom' => ['fr' => 'Gloss'], 'description' => ['fr' => 'Des lèvres douces.']]);
    $this->travel(-3)->days();
    $ancien = Produit::factory()->for($gloss)->create(['nom' => ['fr' => 'Gloss Ancien'], 'affichage_nouveautes' => Produit::NOUVEAUTE_TOUJOURS]);
    $this->travelBack();
    Produit::factory()->for($gloss)->create(['nom' => ['fr' => 'Baume Récent']]);
    Produit::factory()->for($gloss)->create(['nom' => ['fr' => 'Produit Écarté'], 'affichage_nouveautes' => Produit::NOUVEAUTE_JAMAIS]);
    Produit::factory()->for($gloss)->coffret()->create(['nom' => ['fr' => 'Coffret Beauté Luxe']]);
    Gamme::factory()->create(['nom' => ['fr' => 'Gloss permanents']]);

    $this->get('/fr')
        ->assertOk()
        ->assertSee('Des lèvres douces.')
        ->assertSee(__('vitrine.accueil_page.nouveautes'))
        ->assertSeeInOrder(['Gloss Ancien', 'Baume Récent'])
        ->assertDontSee('Produit Écarté')
        ->assertSee('Gloss permanents')
        ->assertSee('Coffret Beauté Luxe')
        ->assertDontSee(__('vitrine.accueil_page.texte'));

    expect($ancien->estNouveau())->toBeTrue();
});

it('annonce l’ouverture tant qu’aucun produit n’est en ligne', function () {
    Produit::factory()->brouillon()->create(['nom' => ['fr' => 'Gloss Secret']]);

    $this->get('/fr')
        ->assertOk()
        ->assertSee(__('vitrine.accueil_page.texte'))
        ->assertDontSee('Gloss Secret')
        ->assertDontSee(__('vitrine.accueil_page.nouveautes'));
});

it('n’affiche pas de meilleures ventes avant les premières ventes', function () {
    Produit::factory()->create();

    $this->get('/fr')->assertDontSee('meilleures ventes', false);
});
