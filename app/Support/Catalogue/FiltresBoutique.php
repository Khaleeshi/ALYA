<?php

namespace App\Support\Catalogue;

use App\Models\Effet;
use App\Models\Ingredient;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Recherche, tri et filtres de la boutique (cahier §5.2), lus dans l'adresse
 * (?q=…&tri=…&prix=…&dispo=1&type=…&effet[]=…&ingredient[]=…) : une page
 * filtrée se partage, se met en favori et marche sans JavaScript.
 *
 * Les filtres ne proposent que ce qui existe dans les produits de la page
 * (les effets et ingrédients d'une catégorie, les tranches de prix utiles).
 */
final class FiltresBoutique
{
    public const TRIS = ['nouveautes', 'prix-croissant', 'prix-decroissant'];

    /** Tranches de prix : [minimum inclus, maximum exclu] en FCFA. */
    public const TRANCHES = [
        'moins-de-5000' => [0, 5000],
        '5000-10000' => [5000, 10000],
        '10000-20000' => [10000, 20000],
        'plus-de-20000' => [20000, null],
    ];

    public const TYPES = ['permanente', 'limitee'];

    /**
     * @param  list<string>  $effets
     * @param  list<string>  $ingredients
     */
    public function __construct(
        public readonly string $recherche = '',
        public readonly string $tri = 'nouveautes',
        public readonly ?string $prix = null,
        public readonly bool $enStock = false,
        public readonly ?string $type = null,
        public readonly array $effets = [],
        public readonly array $ingredients = [],
    ) {}

    public static function depuis(Request $request): self
    {
        $liste = fn (string $cle): array => array_values(array_unique(array_filter(
            array_slice(Arr::wrap($request->query($cle)), 0, 20),
            fn (mixed $valeur): bool => is_string($valeur) && preg_match('/^[a-z0-9-]{1,120}$/', $valeur) === 1,
        )));

        $tri = $request->query('tri');
        $prix = $request->query('prix');
        $type = $request->query('type');

        return new self(
            recherche: mb_substr(trim((string) (is_string($request->query('q')) ? $request->query('q') : '')), 0, 100),
            tri: in_array($tri, self::TRIS, true) ? $tri : 'nouveautes',
            prix: is_string($prix) && array_key_exists($prix, self::TRANCHES) ? $prix : null,
            enStock: $request->query('dispo') === '1',
            type: in_array($type, self::TYPES, true) ? $type : null,
            effets: $liste('effet'),
            ingredients: $liste('ingredient'),
        );
    }

    /**
     * @param  Builder<Produit>  $requete
     * @return Builder<Produit>
     */
    public function appliquer(Builder $requete): Builder
    {
        $requete->recherche($this->recherche);

        if ($this->prix !== null) {
            [$minimum, $maximum] = self::TRANCHES[$this->prix];
            $requete->where('produits.prix', '>=', $minimum)
                ->when($maximum !== null, fn (Builder $q) => $q->where('produits.prix', '<', $maximum));
        }

        if ($this->enStock) {
            $requete->whereHas('variantes', fn (Builder $q) => $q->where('active', true)->where('stock', '>', 0));
        }

        if ($this->type !== null) {
            $requete->where('edition_limitee', $this->type === 'limitee');
        }

        // Plusieurs effets cochés : un produit qui en a au moins un.
        if ($this->effets !== []) {
            $requete->whereHas('effets', fn (Builder $q) => $q->whereIn('slug', $this->effets));
        }

        if ($this->ingredients !== []) {
            $requete->whereHas('ingredients', fn (Builder $q) => $q->whereIn('slug', $this->ingredients));
        }

        return match ($this->tri) {
            'prix-croissant' => $requete->orderBy('produits.prix')->orderBy('produits.id'),
            'prix-decroissant' => $requete->orderByDesc('produits.prix')->orderBy('produits.id'),
            default => $requete->orderByDesc('produits.publie_le')->orderByDesc('produits.id'),
        };
    }

    public function actifs(): bool
    {
        return $this->recherche !== '' || $this->prix !== null || $this->enStock || $this->type !== null
            || $this->effets !== [] || $this->ingredients !== [];
    }

    /**
     * Ce que les filtres peuvent proposer pour ces produits.
     *
     * @param  Builder<Produit>  $base
     * @return array{tranches: list<string>, types: bool, effets: Collection<int, Effet>, ingredients: Collection<int, Ingredient>}
     */
    public static function options(Builder $base): array
    {
        $identifiants = (clone $base)->select('produits.id');

        $tranches = array_values(array_filter(array_keys(self::TRANCHES), function (string $tranche) use ($base): bool {
            [$minimum, $maximum] = self::TRANCHES[$tranche];

            return (clone $base)->where('produits.prix', '>=', $minimum)
                ->when($maximum !== null, fn (Builder $q) => $q->where('produits.prix', '<', $maximum))
                ->exists();
        }));

        return [
            'tranches' => count($tranches) > 1 ? $tranches : [],
            // Le choix permanente / édition limitée n'a de sens que si les deux existent.
            'types' => (clone $base)->where('edition_limitee', true)->exists() && (clone $base)->where('edition_limitee', false)->exists(),
            'effets' => Effet::query()
                ->whereHas('produits', fn (Builder $q) => $q->whereIn('produits.id', $identifiants))
                ->orderBy('position')
                ->orderBy('id')
                ->get(),
            'ingredients' => Ingredient::query()
                ->whereHas('produits', fn (Builder $q) => $q->whereIn('produits.id', $identifiants))
                ->orderBy('id')
                ->get(),
        ];
    }

    /**
     * Filtres en cours, chacun avec l'adresse de la page sans lui.
     *
     * @param  array{effets: Collection<int, Effet>, ingredients: Collection<int, Ingredient>}  $options
     * @return list<array{libelle: string, url: string}>
     */
    public function puces(Request $request, array $options): array
    {
        $puces = [];
        $sans = function (string $cle, ?string $valeur = null) use ($request): string {
            $parametres = Arr::except($request->query(), ['page']);

            if ($valeur === null) {
                unset($parametres[$cle]);
            } else {
                $parametres[$cle] = array_values(array_diff(Arr::wrap($parametres[$cle] ?? []), [$valeur]));
            }

            $chaine = Arr::query($parametres);

            return $request->url().($chaine !== '' ? '?'.$chaine : '');
        };

        if ($this->recherche !== '') {
            $puces[] = ['libelle' => '« '.$this->recherche.' »', 'url' => $sans('q')];
        }

        if ($this->prix !== null) {
            $puces[] = ['libelle' => __('vitrine.boutique.tranches.'.$this->prix), 'url' => $sans('prix')];
        }

        if ($this->enStock) {
            $puces[] = ['libelle' => __('vitrine.boutique.en_stock'), 'url' => $sans('dispo')];
        }

        if ($this->type !== null) {
            $puces[] = ['libelle' => __('vitrine.boutique.types.'.$this->type), 'url' => $sans('type')];
        }

        foreach ($options['effets']->whereIn('slug', $this->effets) as $effet) {
            $puces[] = ['libelle' => $effet->nom, 'url' => $sans('effet', $effet->slug)];
        }

        foreach ($options['ingredients']->whereIn('slug', $this->ingredients) as $ingredient) {
            $puces[] = ['libelle' => $ingredient->nom, 'url' => $sans('ingredient', $ingredient->slug)];
        }

        return $puces;
    }
}
