<?php

namespace App\Models;

use App\Models\Concerns\AvecPhotos;
use App\Models\Concerns\AvecSlug;
use App\Support\Catalogue\TexteRecherche;
use App\Support\Montant;
use Database\Factories\ProduitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Fiche produit (cahier §5.3 et §5.4). Le prix, le stock et la couleur se
 * choisissent sur les variantes : un produit sans choix a une seule
 * variante, sans nom.
 *
 * Une cliente ne voit un produit que s'il est publié, dans ses dates de
 * campagne, et que sa catégorie est visible (scope « visibles »).
 *
 * @property int $categorie_id
 * @property string $nom
 * @property string $slug
 * @property string $nature
 * @property ?string $accroche
 * @property ?string $description
 * @property ?string $bienfaits
 * @property ?string $conseils
 * @property ?string $composition
 * @property ?string $mots_cles
 * @property ?string $meta_titre
 * @property ?string $meta_description
 * @property int $prix
 * @property ?int $cout_interne
 * @property int $seuil_alerte
 * @property bool $edition_limitee
 * @property ?Carbon $campagne_debut_le
 * @property ?Carbon $campagne_fin_le
 * @property string $statut
 * @property ?Carbon $publie_le
 * @property string $affichage_nouveautes
 * @property ?string $texte_recherche
 */
class Produit extends Model implements HasMedia
{
    use AvecPhotos;
    use AvecSlug;

    /** @use HasFactory<ProduitFactory> */
    use HasFactory;

    use HasTranslations;

    public const NATURE_PRODUIT = 'produit';

    public const NATURE_COFFRET = 'coffret';

    public const BROUILLON = 'brouillon';

    public const PUBLIE = 'publie';

    public const ARCHIVE = 'archive';

    public const PHOTOS = 'photos';

    public const NOUVEAUTE_AUTO = 'auto';

    public const NOUVEAUTE_TOUJOURS = 'toujours';

    public const NOUVEAUTE_JAMAIS = 'jamais';

    protected $table = 'produits';

    /** @var list<string> */
    public array $translatable = [
        'nom',
        'accroche',
        'description',
        'bienfaits',
        'conseils',
        'composition',
        'mots_cles',
        'meta_titre',
        'meta_description',
    ];

    protected $fillable = [
        'categorie_id',
        'nom',
        'slug',
        'nature',
        'accroche',
        'description',
        'bienfaits',
        'conseils',
        'composition',
        'mots_cles',
        'meta_titre',
        'meta_description',
        'prix',
        'cout_interne',
        'seuil_alerte',
        'edition_limitee',
        'campagne_debut_le',
        'campagne_fin_le',
        'statut',
        'affichage_nouveautes',
    ];

    /**
     * @return array<string, string>
     */
    public static function statuts(): array
    {
        return [
            self::BROUILLON => 'Brouillon',
            self::PUBLIE => 'Publié',
            self::ARCHIVE => 'Archivé',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function natures(): array
    {
        return [
            self::NATURE_PRODUIT => 'Produit',
            self::NATURE_COFFRET => 'Coffret cadeau',
        ];
    }

    protected function casts(): array
    {
        return [
            'prix' => 'integer',
            'cout_interne' => 'integer',
            'seuil_alerte' => 'integer',
            'edition_limitee' => 'boolean',
            'campagne_debut_le' => 'datetime',
            'campagne_fin_le' => 'datetime',
            'publie_le' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $produit): void {
            // La première mise en ligne date la nouveauté ; elle ne bouge plus ensuite.
            if ($produit->statut === self::PUBLIE && $produit->publie_le === null) {
                $produit->publie_le = now();
            }

            $produit->texte_recherche = $produit->construireTexteRecherche();
        });

        // Journal des actions sensibles (cahier §12.3) : prix et mise en ligne.
        static::updated(function (self $produit): void {
            if ($produit->wasChanged('prix')) {
                JournalActivite::consigner(
                    'catalogue.prix',
                    sprintf('Prix de « %s » : %s → %s', $produit->nom, Montant::fcfa((int) $produit->getOriginal('prix')), Montant::fcfa($produit->prix)),
                    $produit,
                    ['avant' => $produit->getOriginal('prix'), 'apres' => $produit->prix],
                );
            }

            if ($produit->wasChanged('statut')) {
                JournalActivite::consigner(
                    'catalogue.statut',
                    sprintf('« %s » : %s → %s', $produit->nom, self::statuts()[$produit->getOriginal('statut')] ?? $produit->getOriginal('statut'), self::statuts()[$produit->statut] ?? $produit->statut),
                    $produit,
                    ['avant' => $produit->getOriginal('statut'), 'apres' => $produit->statut],
                );
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTOS)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    /**
     * @return BelongsTo<Categorie, $this>
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    /**
     * @return HasMany<Variante, $this>
     */
    public function variantes(): HasMany
    {
        return $this->hasMany(Variante::class)->orderBy('position')->orderBy('id')->chaperone();
    }

    /**
     * Mouvements de stock de toutes les variantes, du plus récent au plus ancien.
     *
     * @return HasManyThrough<MouvementStock, Variante, $this>
     */
    public function mouvementsStock(): HasManyThrough
    {
        return $this->hasManyThrough(MouvementStock::class, Variante::class)->latest('mouvements_stock.cree_le')->latest('mouvements_stock.id');
    }

    /**
     * @return HasMany<Atout, $this>
     */
    public function atouts(): HasMany
    {
        return $this->hasMany(Atout::class)->orderBy('position')->orderBy('id');
    }

    /**
     * @return BelongsToMany<Gamme, $this>
     */
    public function gammes(): BelongsToMany
    {
        return $this->belongsToMany(Gamme::class, 'gamme_produit')->withPivot('position');
    }

    /**
     * Ingrédients clés, dans l'ordre choisi pour la fiche.
     *
     * @return BelongsToMany<Ingredient, $this>
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('position')->orderByPivot('position');
    }

    /**
     * @return BelongsToMany<Effet, $this>
     */
    public function effets(): BelongsToMany
    {
        return $this->belongsToMany(Effet::class);
    }

    /**
     * @param  Builder<self>  $requete
     */
    public function scopeVisibles(Builder $requete): void
    {
        $maintenant = now();

        $requete->where('statut', self::PUBLIE)
            ->where(fn (Builder $q) => $q->whereNull('campagne_debut_le')->orWhere('campagne_debut_le', '<=', $maintenant))
            ->where(fn (Builder $q) => $q->whereNull('campagne_fin_le')->orWhere('campagne_fin_le', '>=', $maintenant))
            ->whereHas('categorie', fn (Builder $q) => $q->where('en_ligne', true));
    }

    /**
     * Nouveautés de l'accueil (cahier §5.1) : les plus récentes mises en ligne,
     * avec l'ajustement de l'administration (« toujours » passe devant,
     * « jamais » est écarté).
     *
     * @param  Builder<self>  $requete
     */
    public function scopeNouveautes(Builder $requete): void
    {
        $requete->visibles()
            ->where('affichage_nouveautes', '!=', self::NOUVEAUTE_JAMAIS)
            ->orderByRaw('case when affichage_nouveautes = ? then 0 else 1 end', [self::NOUVEAUTE_TOUJOURS])
            ->orderByDesc('publie_le')
            ->orderByDesc('id');
    }

    /**
     * Chaque mot saisi doit se retrouver dans le nom, les mots-clés, la
     * catégorie, les couleurs ou les ingrédients clés.
     *
     * @param  Builder<self>  $requete
     */
    public function scopeRecherche(Builder $requete, ?string $saisie): void
    {
        foreach (TexteRecherche::mots($saisie) as $mot) {
            $requete->where('texte_recherche', 'like', '%'.$mot.'%');
        }
    }

    public function estVisible(): bool
    {
        return $this->statut === self::PUBLIE
            && ($this->campagne_debut_le === null || $this->campagne_debut_le->isPast())
            && ($this->campagne_fin_le === null || $this->campagne_fin_le->isFuture())
            && $this->categorie->en_ligne;
    }

    public function estCoffret(): bool
    {
        return $this->nature === self::NATURE_COFFRET;
    }

    public function estNouveau(): bool
    {
        return $this->publie_le !== null
            && $this->publie_le->greaterThanOrEqualTo(now()->subDays((int) config('boutique.catalogue.nouveaute_jours')));
    }

    public function photoPrincipale(): ?Media
    {
        return $this->getFirstMedia(self::PHOTOS);
    }

    /**
     * Variantes proposées aux clientes (les variantes désactivées sont cachées).
     *
     * @return Collection<int, Variante>
     */
    public function variantesActives(): Collection
    {
        return $this->variantes->where('active', true)->values();
    }

    /**
     * Prix le plus bas parmi les variantes proposées : « à partir de » quand
     * les prix diffèrent.
     */
    public function prixMinimum(): int
    {
        return $this->variantesActives()->min(fn (Variante $variante): int => $variante->prixFinal()) ?? $this->prix;
    }

    public function aPlusieursPrix(): bool
    {
        return $this->variantesActives()->map(fn (Variante $variante): int => $variante->prixFinal())->unique()->count() > 1;
    }

    public function enStock(): bool
    {
        return $this->variantesActives()->contains(fn (Variante $variante): bool => $variante->stock > 0);
    }

    /**
     * Recalcule le texte de recherche après un changement de variantes,
     * d'ingrédients ou d'effets (enregistrés après le produit lui-même).
     */
    public function actualiserTexteRecherche(): void
    {
        $texte = $this->construireTexteRecherche();

        if ($texte !== $this->texte_recherche) {
            $this->texte_recherche = $texte;
            $this->saveQuietly();
        }
    }

    private function construireTexteRecherche(): string
    {
        $morceaux = [
            ...array_values($this->getTranslations('nom')),
            ...array_values($this->getTranslations('mots_cles')),
            ...array_values(Categorie::find($this->categorie_id)?->getTranslations('nom') ?? []),
        ];

        if ($this->exists) {
            foreach ($this->variantes()->get() as $variante) {
                array_push($morceaux, $variante->reference, ...array_values($variante->getTranslations('nom')));
            }

            foreach ([...$this->ingredients()->get(), ...$this->effets()->get()] as $element) {
                array_push($morceaux, ...array_values($element->getTranslations('nom')));
            }
        }

        return TexteRecherche::normaliser(implode(' ', $morceaux));
    }
}
