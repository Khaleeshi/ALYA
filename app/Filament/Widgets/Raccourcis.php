<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Gammes\GammeResource;
use App\Filament\Resources\JournalActivites\JournalActiviteResource;
use App\Filament\Resources\Produits\ProduitResource;
use App\Filament\Resources\Utilisateurs\UtilisateurResource;
use Filament\Widgets\Widget;

/**
 * « Actions rapides » (maquette 6), limitées à ce qui existe déjà et à ce que
 * la personne connectée a le droit de faire. Les actions promotion et
 * campagne s'ajouteront avec leurs écrans.
 */
class Raccourcis extends Widget
{
    protected string $view = 'filament.widgets.raccourcis';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    // Des liens seulement, sans requête lourde : inutile d'attendre un second chargement.
    protected static bool $isLazy = false;

    /**
     * @return list<array{libelle: string, icone: string, url: string, externe: bool}>
     */
    public function raccourcis(): array
    {
        $raccourcis = [];

        if (ProduitResource::canCreate()) {
            $raccourcis[] = ['libelle' => 'Ajouter un produit', 'icone' => 'sac', 'url' => ProduitResource::getUrl('create'), 'externe' => false];
        }

        if (GammeResource::canCreate()) {
            $raccourcis[] = ['libelle' => 'Créer une collection', 'icone' => 'diamant', 'url' => GammeResource::getUrl('index', ['action' => 'create']), 'externe' => false];
        }

        if (UtilisateurResource::canCreate()) {
            $raccourcis[] = ['libelle' => 'Ajouter un membre du personnel', 'icone' => 'compte', 'url' => UtilisateurResource::getUrl('create'), 'externe' => false];
        }

        if (JournalActiviteResource::canViewAny()) {
            $raccourcis[] = ['libelle' => "Consulter le journal d'activité", 'icone' => 'recu', 'url' => JournalActiviteResource::getUrl(), 'externe' => false];
        }

        $raccourcis[] = ['libelle' => 'Voir ma boutique', 'icone' => 'boutique', 'url' => route_langue('accueil'), 'externe' => true];

        return $raccourcis;
    }
}
