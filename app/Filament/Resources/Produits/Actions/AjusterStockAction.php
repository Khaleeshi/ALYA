<?php

namespace App\Filament\Resources\Produits\Actions;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Variante;
use App\Support\Catalogue\Stock;
use App\Support\Catalogue\StockInsuffisant;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

/**
 * « Ajuster le stock » : à la réception d'une livraison, on saisit la
 * quantité reçue ; après un inventaire, on saisit ce qu'on a compté et
 * l'écart se calcule tout seul. Chaque ajustement devient un mouvement noté.
 */
final class AjusterStockAction
{
    public static function make(): Action
    {
        return Action::make('ajusterStock')
            ->label('Ajuster le stock')
            ->icon(Heroicon::OutlinedArrowsUpDown)
            ->color('gray')
            ->modalHeading(fn (Produit $record): string => 'Ajuster le stock : '.$record->nom)
            ->modalSubmitActionLabel('Enregistrer')
            ->authorize(fn (Produit $record): bool => (bool) auth('personnel')->user()?->can('update', $record))
            ->schema(fn (Produit $record): array => [
                Select::make('variante_id')
                    ->label('Couleur ou format')
                    ->options(self::variantes($record))
                    ->default(array_key_first(self::variantes($record)))
                    ->selectablePlaceholder(false)
                    ->required(),
                Radio::make('motif')
                    ->label('Pourquoi ?')
                    ->options([
                        MouvementStock::REAPPROVISIONNEMENT => 'J’ai reçu de la marchandise',
                        MouvementStock::CORRECTION => 'J’ai fait l’inventaire',
                    ])
                    ->default(MouvementStock::REAPPROVISIONNEMENT)
                    ->required()
                    ->live(),
                TextInput::make('quantite')
                    ->label(fn (Get $get): string => $get('motif') === MouvementStock::CORRECTION ? 'Quantité comptée en rayon' : 'Quantité reçue')
                    ->numeric()
                    ->integer()
                    ->minValue(fn (Get $get): int => $get('motif') === MouvementStock::CORRECTION ? 0 : 1)
                    ->required(),
                TextInput::make('note')
                    ->label('Note')
                    ->placeholder('Ex. : livraison du fournisseur, casse, inventaire de fin de mois')
                    ->maxLength(255),
            ])
            ->action(function (array $data, Produit $record, Action $action): void {
                /** @var Variante $variante */
                $variante = $record->variantes()->findOrFail($data['variante_id']);
                $saisie = (int) $data['quantite'];
                $ecart = $data['motif'] === MouvementStock::CORRECTION ? $saisie - $variante->stock : $saisie;

                if ($ecart === 0) {
                    Notification::make()->info()->title('Rien à changer')->body('Le stock compté est déjà celui du site.')->send();

                    return;
                }

                try {
                    Stock::ajuster($variante, $ecart, $data['motif'], $data['note'] ?? null);
                } catch (StockInsuffisant $erreur) {
                    Notification::make()->danger()->title('Stock insuffisant')->body($erreur->getMessage())->send();
                    $action->halt();
                }

                Notification::make()
                    ->success()
                    ->title('Stock mis à jour')
                    ->body(sprintf('%s : %+d, nouveau stock %d.', $variante->libelle(), $ecart, $variante->stock))
                    ->send();
            });
    }

    /**
     * @return array<int, string>
     */
    private static function variantes(Produit $produit): array
    {
        return $produit->variantes()->get()
            ->mapWithKeys(fn (Variante $variante): array => [
                $variante->id => sprintf('%s (stock : %d)', filled($variante->nom) ? $variante->nom : 'Produit sans choix', $variante->stock),
            ])
            ->all();
    }
}
