<?php

namespace Database\Seeders;

use App\Models\Atout;
use App\Models\Categorie;
use App\Models\Effet;
use App\Models\Gamme;
use App\Models\Ingredient;
use App\Models\Produit;
use App\Models\Variante;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Catalogue d'essai pour le poste de développement et la recette : noms et
 * prix repris des maquettes, sans photos. Refusé en production : aucune
 * donnée de démonstration en ligne (cahier §13 et §17).
 *
 * php artisan db:seed --class=CatalogueDemonstrationSeeder
 */
class CatalogueDemonstrationSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Le catalogue d’essai ne va jamais en production.');
        }

        $this->call(CategoriesSeeder::class);

        $gloss = Categorie::where('slug', 'gloss')->sole();
        $skincare = Categorie::where('slug', 'skincare')->sole();
        $maquillage = Categorie::where('slug', 'maquillage')->sole();

        $gloss->update(['description' => ['fr' => 'Des lèvres douces, brillantes et irrésistibles.']]);
        $skincare->update(['description' => ['fr' => 'Une peau éclatante et en bonne santé.']]);
        $maquillage->update(['description' => ['fr' => 'Pour sublimer votre beauté en toutes occasions.']]);

        $effets = collect(['Hydratation', 'Brillance', 'Repulpant', 'Éclat', 'Tenue longue durée'])
            ->mapWithKeys(fn (string $nom, int $i): array => [$nom => Effet::firstOrCreate(['slug' => str($nom)->slug()->value()], ['nom' => ['fr' => $nom], 'position' => $i])]);

        $ingredients = collect([
            'Huile de jojoba' => 'Nourrit et protège',
            'Beurre d’amande douce' => 'Adoucit et assouplit',
            'Huile de ricin' => 'Favorise l’hydratation',
            'Vitamine E' => 'Protège et répare',
            'Huile de coco' => 'Apporte de la brillance',
            'Huile d’avocat' => 'Nourrit en profondeur',
        ])->mapWithKeys(fn (string $bienfait, string $nom): array => [$nom => Ingredient::firstOrCreate(['slug' => str($nom)->slug()->value()], ['nom' => ['fr' => $nom], 'bienfait' => ['fr' => $bienfait]])]);

        $permanents = Gamme::firstOrCreate(['slug' => 'gloss-permanents'], ['nom' => ['fr' => 'Gloss permanents'], 'type' => Gamme::PERMANENTE, 'accroche' => ['fr' => 'Des teintes pour chaque envie.'], 'position' => 0]);
        $baumes = Gamme::firstOrCreate(['slug' => 'baumes-a-levres'], ['nom' => ['fr' => 'Baumes à lèvres'], 'type' => Gamme::PERMANENTE, 'accroche' => ['fr' => 'Hydratation et douceur au quotidien.'], 'position' => 1]);
        $roseElegance = Gamme::firstOrCreate(['slug' => 'rose-elegance'], ['nom' => ['fr' => 'Collection Rose Élégance'], 'type' => Gamme::EDITION_LIMITEE, 'accroche' => ['fr' => 'Des teintes uniques pour briller autrement.'], 'position' => 2]);

        if (Produit::where('slug', 'gloss-amani')->exists()) {
            return;
        }

        $glossAmani = $this->produit($gloss, 'Gloss Amani', 5000, [
            'accroche' => 'Un gloss nude lumineux qui apporte une brillance intense tout en nourrissant vos lèvres. Sa texture légère et non collante révèle une bouche douce, hydratée et naturellement plus pulpeuse.',
            'description' => "Le Gloss Amani est l'alliance parfaite entre soin et beauté. Sa formule enrichie en huiles naturelles hydrate vos lèvres en profondeur tout en leur apportant une brillance élégante et durable.\n\nIdéal pour un look sophistiqué au quotidien.",
            'bienfaits' => "Hydrate en profondeur\nApporte une brillance intense\nLisse et adoucit les lèvres\nEffet repulpant naturel\nNon collant\nConvient à un usage quotidien",
            'conseils' => "Appliquer seul ou sur un rouge à lèvres.\nRenouveler l'application dans la journée selon vos envies.",
            'composition' => 'Polybutene, Ricinus Communis Seed Oil, Simmondsia Chinensis Seed Oil, Tocopheryl Acetate, Parfum.',
            'mots_cles' => 'lip gloss brillant repulpant',
        ], ['Nude Glow' => [24, '#c98f7a'], 'Pink Diamond' => [3, '#e07f8c'], 'Berry Shine' => [12, '#b4566a'], 'Chocolate' => [0, '#7b4a3a'], 'Rose Nude' => [9, '#d9a092']]);
        $glossAmani->ingredients()->attach($ingredients->only(['Huile de jojoba', 'Beurre d’amande douce', 'Huile de ricin', 'Vitamine E', 'Huile de coco', 'Huile d’avocat'])->values()->mapWithKeys(fn (Ingredient $i, int $rang): array => [$i->id => ['position' => $rang]])->all());
        $glossAmani->effets()->attach([$effets['Hydratation']->id, $effets['Brillance']->id, $effets['Repulpant']->id]);
        foreach ([['levres', 'Lèvres plus douces et hydratées'], ['eclat', 'Brillance intense non collante'], ['feuille', 'Enrichi en huiles nourrissantes'], ['bouclier', 'Convient à tous les types de lèvres']] as $rang => [$icone, $texte]) {
            Atout::create(['produit_id' => $glossAmani->id, 'icone' => $icone, 'texte' => ['fr' => $texte], 'position' => $rang]);
        }
        $permanents->produits()->attach($glossAmani);

        $baume = $this->produit($gloss, 'Baume Douceur', 3500, ['accroche' => 'Un baume fondant qui nourrit et protège vos lèvres.'], ['Naturel' => [30, '#e6c1a8'], 'Rosé' => [14, '#e4a2a0']]);
        $baume->effets()->attach([$effets['Hydratation']->id]);
        $baume->ingredients()->attach([$ingredients['Beurre d’amande douce']->id, $ingredients['Vitamine E']->id]);
        $baumes->produits()->attach($baume);

        $chantilly = $this->produit($gloss, 'Chantilly Lèvres', 4500, ['accroche' => 'Des soins gourmands et moussants.'], ['Fraise' => [8, '#e58a9a'], 'Vanille' => [2, '#f1d6b8']]);
        $chantilly->effets()->attach([$effets['Hydratation']->id, $effets['Repulpant']->id]);

        $rose = $this->produit($gloss, 'Gloss Rose Élégance', 6500, ['accroche' => 'Une édition limitée aux reflets rosés.'], ['Rose Élégance' => [15, '#d9798a']], ['edition_limitee' => true]);
        $rose->effets()->attach([$effets['Brillance']->id]);
        $roseElegance->produits()->attach($rose);

        $serum = $this->produit($skincare, 'Sérum Éclat', 12000, ['accroche' => 'Un sérum léger pour un teint lumineux.'], ['30 ml' => [18, null]]);
        $serum->effets()->attach([$effets['Éclat']->id, $effets['Hydratation']->id]);
        $serum->ingredients()->attach([$ingredients['Vitamine E']->id, $ingredients['Huile de jojoba']->id]);

        $creme = $this->produit($skincare, 'Crème Hydratante', 9500, ['accroche' => 'Une crème douce pour une peau éclatante et en bonne santé.'], ['50 ml' => [0, null]]);
        $creme->effets()->attach([$effets['Hydratation']->id]);

        $poudre = $this->produit($maquillage, 'Poudre Soleil', 8000, ['accroche' => 'Un teint hâlé et lumineux.'], ['Doré' => [7, '#c58b52'], 'Bronze' => [11, '#9c6b3f']]);
        $poudre->effets()->attach([$effets['Éclat']->id, $effets['Tenue longue durée']->id]);

        $this->produit($maquillage, 'Coffret Lèvres Parfaites', 15000, ['accroche' => 'Le cadeau idéal pour sublimer vos lèvres.'], ['' => [6, null]], ['nature' => Produit::NATURE_COFFRET]);
        $this->produit($skincare, 'Coffret Routine Éclat', 25000, ['accroche' => 'Une routine complète pour une peau rayonnante.'], ['' => [4, null]], ['nature' => Produit::NATURE_COFFRET]);
        $this->produit($maquillage, 'Coffret Beauté Luxe', 35000, ['accroche' => 'Un coffret élégant pour toutes les occasions.'], ['' => [2, null]], ['nature' => Produit::NATURE_COFFRET]);

        Produit::all()->each->actualiserTexteRecherche();
    }

    /**
     * @param  array<string, string>  $textes
     * @param  array<string, array{0: int, 1: ?string}>  $variantes  nom => [stock, couleur]
     * @param  array<string, mixed>  $attributs
     */
    private function produit(Categorie $categorie, string $nom, int $prix, array $textes, array $variantes, array $attributs = []): Produit
    {
        $produit = Produit::create([
            'categorie_id' => $categorie->id,
            'nom' => ['fr' => $nom],
            'prix' => $prix,
            'statut' => Produit::PUBLIE,
            ...array_map(fn (string $texte): array => ['fr' => $texte], $textes),
            ...$attributs,
        ]);

        $position = 0;

        foreach ($variantes as $nomVariante => [$stock, $couleur]) {
            Variante::create([
                'produit_id' => $produit->id,
                'nom' => $nomVariante === '' ? null : ['fr' => $nomVariante],
                'couleur' => $couleur,
                'stock' => $stock,
                'position' => $position++,
            ]);
        }

        return $produit;
    }
}
