<?php

namespace App\Support\Panier;

use App\Models\Cliente;
use App\Models\LignePanier;
use App\Models\Panier;
use App\Models\Variante;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Le panier de la personne qui navigue (cahier §6.1).
 *
 * - Visiteuse sans compte : panier retrouvé par le jeton du cookie « amani_panier ».
 * - Cliente connectée : panier de son compte. À la connexion, le panier de
 *   visiteuse rejoint celui du compte (FusionnerPanier).
 * - Quantité d'une ligne : au moins 1, au plus le stock et la limite par commande.
 *   Rien n'est réservé : la disponibilité est revue à chaque affichage.
 */
final class GestionPanier
{
    public const COOKIE = 'amani_panier';

    private ?Panier $panier = null;

    /** Requête pour laquelle le panier a été cherché (le service vit le temps d'une requête). */
    private ?int $requete = null;

    public function actuel(): ?Panier
    {
        if ($this->requete === spl_object_id(request())) {
            return $this->panier;
        }

        $this->requete = spl_object_id(request());
        $cliente = auth('web')->user();

        if ($cliente !== null) {
            return $this->panier = Panier::query()->firstWhere('cliente_id', $cliente->getKey());
        }

        $jeton = request()->cookie(self::COOKIE);

        return $this->panier = is_string($jeton) && $jeton !== ''
            ? Panier::query()->whereNull('cliente_id')->firstWhere('jeton', $jeton)
            : null;
    }

    public function nombreArticles(): int
    {
        return (int) $this->actuel()?->lignes()->sum('quantite');
    }

    /**
     * Ajoute des exemplaires d'une variante. Renvoie la ligne, et vrai si la
     * quantité a dû être limitée au stock ou à la limite par commande.
     *
     * @return array{0: LignePanier, 1: bool}
     *
     * @throws ArticleIndisponible
     */
    public function ajouter(Variante $variante, int $quantite): array
    {
        $variante->loadMissing('produit.categorie');

        if (! $variante->active || $variante->stock <= 0 || ! $variante->produit->estVisible()) {
            throw new ArticleIndisponible($variante);
        }

        $panier = $this->ouvrir();
        $ligne = $panier->lignes()->firstOrNew(['variante_id' => $variante->getKey()]);
        $voulue = ($ligne->exists ? $ligne->quantite : 0) + max(1, $quantite);

        $ligne->quantite = min($voulue, self::quantiteMaximale($variante));
        $ligne->save();
        $panier->touch();

        return [$ligne, $voulue > $ligne->quantite];
    }

    /**
     * Change la quantité d'une ligne. Renvoie vrai si elle a dû être limitée.
     */
    public function modifier(LignePanier $ligne, int $quantite): bool
    {
        $maximum = max(1, self::quantiteMaximale($ligne->variante));
        $ligne->update(['quantite' => min(max(1, $quantite), $maximum)]);
        $ligne->panier->touch();

        return $quantite > $maximum;
    }

    public function retirer(LignePanier $ligne): void
    {
        $ligne->delete();
        $ligne->panier->touch();
    }

    public function vider(): void
    {
        $this->actuel()?->lignes()->delete();
    }

    /**
     * Une ligne du panier en cours ; 404 si elle appartient à un autre panier.
     */
    public function ligne(int $identifiant): LignePanier
    {
        $ligne = $this->actuel()?->lignes()->with('variante.produit.categorie')->find($identifiant);

        abort_if($ligne === null, 404);

        return $ligne;
    }

    /**
     * Revoit les quantités avec le stock du moment : une ligne qui dépasse
     * le stock y est ramenée. Renvoie les noms des articles ajustés.
     *
     * @param  Collection<int, LignePanier>  $lignes
     * @return list<string>
     */
    public function verifier(Collection $lignes): array
    {
        $ajustes = [];

        foreach ($lignes as $ligne) {
            $maximum = self::quantiteMaximale($ligne->variante);

            if ($ligne->estDisponible() && $ligne->quantite > $maximum) {
                $ligne->update(['quantite' => $maximum]);
                $ajustes[] = $ligne->variante->libelle();
            }
        }

        return $ajustes;
    }

    /**
     * À la connexion : le panier de visiteuse rejoint celui du compte, les
     * quantités s'additionnent dans les limites du stock.
     */
    public function fusionnerDans(Cliente $cliente): void
    {
        $jeton = request()->cookie(self::COOKIE);
        $invite = is_string($jeton) && $jeton !== ''
            ? Panier::query()->whereNull('cliente_id')->firstWhere('jeton', $jeton)
            : null;

        if ($invite !== null) {
            DB::transaction(function () use ($invite, $cliente): void {
                $compte = Panier::query()->firstOrCreate(['cliente_id' => $cliente->getKey()]);

                foreach ($invite->lignes()->with('variante')->get() as $ligne) {
                    $existante = $compte->lignes()->firstOrNew(['variante_id' => $ligne->variante_id]);
                    $somme = ($existante->exists ? $existante->quantite : 0) + $ligne->quantite;
                    $existante->quantite = min($somme, max(1, self::quantiteMaximale($ligne->variante)));
                    $existante->save();
                }

                $invite->delete();
                $compte->touch();
            });
        }

        if ($jeton !== null) {
            Cookie::queue(Cookie::forget(self::COOKIE));
        }

        $this->requete = null;
    }

    public static function quantiteMaximale(Variante $variante): int
    {
        return min($variante->stock, (int) config('boutique.panier.quantite_max'));
    }

    private function ouvrir(): Panier
    {
        if ($panier = $this->actuel()) {
            return $panier;
        }

        $cliente = auth('web')->user();

        if ($cliente !== null) {
            return $this->panier = Panier::query()->create(['cliente_id' => $cliente->getKey()]);
        }

        $jeton = Str::random(48);
        Cookie::queue(Cookie::make(
            self::COOKIE,
            $jeton,
            (int) config('boutique.panier.cookie_jours') * 24 * 60,
            secure: config('session.secure'),
            httpOnly: true,
            sameSite: 'lax',
        ));

        return $this->panier = Panier::query()->create(['jeton' => $jeton]);
    }
}
