<?php

use App\Filament\Pages\TableauDeBord;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Produits\Pages\CreateProduit;
use App\Filament\Resources\Produits\Pages\EditProduit;
use App\Filament\Resources\Produits\Pages\ListProduits;
use App\Filament\Widgets\Raccourcis;
use App\Filament\Widgets\StockFaible;
use App\Models\Categorie;
use App\Models\JournalActivite;
use App\Models\MouvementStock;
use App\Models\Produit;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;

beforeEach(function () {
    Filament::setCurrentPanel('administration');
    $this->proprietaire = proprietaire(['prenom' => 'Ndeye', 'app_authentication_secret' => secretDoubleAuthentification()]);
    $this->actingAs($this->proprietaire, 'personnel');
    $this->categorie = Categorie::factory()->create(['nom' => ['fr' => 'Gloss']]);
});

it('crée un produit avec ses couleurs, leur stock de départ et une trace au journal', function () {
    $annulerFaux = Repeater::fake();

    livewire(CreateProduit::class)
        ->fillForm([
            'nom' => ['fr' => 'Gloss Amani'],
            'accroche' => ['fr' => 'Une brillance lumineuse.'],
            'categorie_id' => $this->categorie->id,
            'nature' => Produit::NATURE_PRODUIT,
            'statut' => Produit::PUBLIE,
            'prix' => 5000,
            'seuil_alerte' => 5,
            'variantes' => [
                ['nom' => ['fr' => 'Nude Glow'], 'couleur' => '#d79b85', 'stock' => 12, 'active' => true],
                ['nom' => ['fr' => 'Rose Diamant'], 'couleur' => '#e3828f', 'stock' => 0, 'active' => true],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $annulerFaux();

    $produit = Produit::with('variantes')->sole();

    expect($produit->nom)->toBe('Gloss Amani')
        ->and($produit->slug)->toBe('gloss-amani')
        ->and($produit->publie_le)->not->toBeNull()
        ->and($produit->variantes->pluck('stock')->all())->toBe([12, 0])
        ->and($produit->variantes->first()->reference)->toStartWith('AMN-')
        ->and(MouvementStock::sole()->utilisateur_id)->toBe($this->proprietaire->id)
        ->and(Produit::recherche('rose diamant')->count())->toBe(1)
        ->and(JournalActivite::where('action', 'catalogue.creation')->sole()->description)->toContain('Gloss Amani');
});

it('demande au moins un nom, une catégorie et un prix', function () {
    livewire(CreateProduit::class)
        ->fillForm(['nom' => ['fr' => ''], 'categorie_id' => null, 'prix' => null])
        ->call('create')
        ->assertHasFormErrors(['nom.fr' => 'required', 'categorie_id' => 'required', 'prix' => 'required']);
});

it('ne laisse pas changer le stock depuis le formulaire', function () {
    $produit = Produit::factory()->create();
    $annulerFaux = Repeater::fake();

    $page = livewire(EditProduit::class, ['record' => $produit->id]);
    $variantes = $page->get('data.variantes');
    $variantes[array_key_first($variantes)]['stock'] = 99;

    $page->fillForm(['variantes' => $variantes])
        ->call('save')
        ->assertHasNoFormErrors();

    $annulerFaux();

    expect($produit->variantes()->sole()->stock)->toBe(10);
});

it('ajuste le stock : livraison reçue, puis inventaire', function () {
    $produit = Produit::factory()->create();
    $variante = $produit->variantes()->sole();

    livewire(EditProduit::class, ['record' => $produit->id])
        ->callAction('ajusterStock', data: ['variante_id' => $variante->id, 'motif' => MouvementStock::REAPPROVISIONNEMENT, 'quantite' => 5, 'note' => 'Livraison'])
        ->assertHasNoActionErrors()
        ->callAction('ajusterStock', data: ['variante_id' => $variante->id, 'motif' => MouvementStock::CORRECTION, 'quantite' => 13])
        ->assertHasNoActionErrors();

    expect($variante->fresh()->stock)->toBe(13)
        ->and($variante->mouvements()->pluck('quantite')->all())->toBe([-2, 5, 10])
        ->and(JournalActivite::where('action', 'catalogue.stock')->count())->toBe(2);
});

it('ne supprime qu’un brouillon jamais publié', function () {
    $publie = Produit::factory()->create();
    $brouillon = Produit::factory()->brouillon()->create();

    livewire(EditProduit::class, ['record' => $publie->id])
        ->assertActionHidden(DeleteAction::class);

    livewire(EditProduit::class, ['record' => $brouillon->id])
        ->callAction(DeleteAction::class);

    expect(Produit::pluck('id')->all())->toBe([$publie->id])
        ->and(JournalActivite::where('action', 'catalogue.suppression')->exists())->toBeTrue();
});

it('montre le coût interne à la propriétaire seulement', function () {
    $produit = Produit::factory()->create();

    livewire(EditProduit::class, ['record' => $produit->id])->assertFormFieldVisible('cout_interne');

    $this->actingAs(administrateur(), 'personnel');
    livewire(EditProduit::class, ['record' => $produit->id])->assertFormFieldHidden('cout_interne');
});

it('liste les produits et les retrouve par recherche', function () {
    $gloss = Produit::factory()->create(['nom' => ['fr' => 'Gloss Éclat']]);
    $baume = Produit::factory()->create(['nom' => ['fr' => 'Baume Douceur']]);

    livewire(ListProduits::class)
        ->assertCanSeeTableRecords([$gloss, $baume])
        ->searchTable('eclat')
        ->assertCanSeeTableRecords([$gloss])
        ->assertCanNotSeeTableRecords([$baume]);
});

it('signale au tableau de bord les couleurs au seuil d’alerte ou épuisées', function () {
    $produit = Produit::factory()->avecTeintes(['Nude' => 0, 'Rose' => 3, 'Prune' => 40])->create(['seuil_alerte' => 5]);
    Produit::factory()->avecTeintes(['Ancienne' => 0])->create(['statut' => Produit::ARCHIVE]);
    [$nude, $rose, $prune] = $produit->variantes;

    livewire(StockFaible::class)
        ->assertCanSeeTableRecords([$nude, $rose])
        ->assertCanNotSeeTableRecords([$prune])
        ->assertCountTableRecords(2);
});

it('propose d’ajouter un produit et de créer une collection', function () {
    livewire(Raccourcis::class)
        ->assertSee('Ajouter un produit')
        ->assertSee('Créer une collection');

    $this->get(TableauDeBord::getUrl())->assertOk();
});

it('ne supprime pas une catégorie qui a des produits', function () {
    $vide = Categorie::factory()->create();
    Produit::factory()->for($this->categorie)->create();

    livewire(ManageCategories::class)
        ->assertActionHidden(TestAction::make(DeleteAction::class)->table($this->categorie))
        ->callAction(TestAction::make(DeleteAction::class)->table($vide));

    expect(Categorie::pluck('id')->all())->toBe([$this->categorie->id]);
});
