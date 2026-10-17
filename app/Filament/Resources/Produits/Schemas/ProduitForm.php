<?php

namespace App\Filament\Resources\Produits\Schemas;

use App\Filament\Support\ChampsTraduits;
use App\Models\Atout;
use App\Models\Categorie;
use App\Models\Effet;
use App\Models\Gamme;
use App\Models\Ingredient;
use App\Models\Produit;
use App\Models\Variante;
use App\Support\Acces\Permissions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

/**
 * Fiche produit de l'administration. Colonne principale : ce que voit la
 * cliente (textes, couleurs, photos, atouts, ingrédients) ; colonne de
 * droite : publication, prix, dates.
 */
class ProduitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Présentation')
                        ->schema([
                            ChampsTraduits::pour(fn (string $langue, bool $principale): array => [
                                TextInput::make("nom.{$langue}")
                                    ->label('Nom')
                                    ->required($principale)
                                    ->maxLength(120),
                                Textarea::make("accroche.{$langue}")
                                    ->label('Accroche')
                                    ->helperText('Une ou deux phrases, sous le prix sur la fiche.')
                                    ->rows(2)
                                    ->maxLength(300),
                                Textarea::make("description.{$langue}")
                                    ->label('Description')
                                    ->helperText('Un paragraphe par ligne vide.')
                                    ->rows(6)
                                    ->maxLength(5000),
                            ]),
                        ]),

                    Section::make('Couleurs, formats et stock')
                        ->description('Une ligne par couleur ou par format. Un produit sans choix garde une seule ligne, sans nom.')
                        ->schema([
                            self::variantes(),
                        ]),

                    Section::make('Photos')
                        ->description('La première photo est la photo principale. Idéalement 2400 px de large, fond nude, lumière douce (cahier §2.3).')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('photos')
                                ->hiddenLabel()
                                ->collection(Produit::PHOTOS)
                                ->multiple()
                                ->reorderable()
                                ->image()
                                ->maxFiles(12)
                                ->panelLayout('grid'),
                        ]),

                    Section::make('Détails de la fiche')
                        ->description('Onglets « Bienfaits », « Conseils d’utilisation » et « Ingrédients » de la fiche.')
                        ->collapsible()
                        ->schema([
                            ChampsTraduits::pour(fn (string $langue): array => [
                                Textarea::make("bienfaits.{$langue}")
                                    ->label('Bienfaits')
                                    ->helperText('Un bienfait par ligne : ils s’affichent en liste cochée.')
                                    ->rows(5),
                                Textarea::make("conseils.{$langue}")
                                    ->label('Conseils d’utilisation')
                                    ->helperText('Un conseil par ligne.')
                                    ->rows(4),
                                Textarea::make("composition.{$langue}")
                                    ->label('Composition complète')
                                    ->helperText('La liste des ingrédients imprimée sur l’emballage.')
                                    ->rows(3),
                            ]),
                        ]),

                    Section::make('Atouts')
                        ->description('Quatre au plus, illustrés d’une icône, sous les boutons d’achat.')
                        ->collapsible()
                        ->schema([
                            Repeater::make('atouts')
                                ->hiddenLabel()
                                ->relationship()
                                ->orderColumn('position')
                                ->maxItems(Atout::MAXIMUM)
                                ->defaultItems(0)
                                ->addActionLabel('Ajouter un atout')
                                ->columns(3)
                                ->schema([
                                    Select::make('icone')
                                        ->label('Icône')
                                        ->options(Atout::icones())
                                        ->required(),
                                    ChampsTraduits::pour(fn (string $langue, bool $principale): array => [
                                        TextInput::make("texte.{$langue}")
                                            ->label('Texte')
                                            ->placeholder('Ex. : Brillance intense non collante')
                                            ->required($principale)
                                            ->maxLength(60),
                                    ])->columnSpan(2),
                                ]),
                        ]),

                    Section::make('Ingrédients clés et effets')
                        ->description('Affichés sur la fiche et proposés comme filtres dans la boutique.')
                        ->collapsible()
                        ->columns(2)
                        ->schema([
                            Select::make('ingredients')
                                ->label('Ingrédients clés')
                                ->relationship('ingredients', 'nom')
                                ->getOptionLabelFromRecordUsing(fn (Ingredient $ingredient): string => $ingredient->nom)
                                ->multiple()
                                ->preload(),
                            Select::make('effets')
                                ->label('Effets et besoins')
                                ->relationship('effets', 'nom')
                                ->getOptionLabelFromRecordUsing(fn (Effet $effet): string => $effet->nom)
                                ->multiple()
                                ->preload(),
                        ]),

                    Section::make('Référencement et recherche')
                        ->collapsed()
                        ->schema([
                            TextInput::make('slug')
                                ->label('Adresse de la fiche')
                                ->prefix('…/produit/')
                                ->helperText('Laissée vide, elle se déduit du nom. À changer avec prudence : les anciens liens ne marcheront plus.')
                                ->alphaDash()
                                ->maxLength(160)
                                ->unique(ignoreRecord: true),
                            ChampsTraduits::pour(fn (string $langue): array => [
                                TextInput::make("meta_titre.{$langue}")
                                    ->label('Titre pour les moteurs de recherche')
                                    ->helperText('Vide : le nom du produit.')
                                    ->maxLength(70),
                                Textarea::make("meta_description.{$langue}")
                                    ->label('Description pour les moteurs de recherche')
                                    ->helperText('Vide : l’accroche.')
                                    ->rows(2)
                                    ->maxLength(160),
                                TextInput::make("mots_cles.{$langue}")
                                    ->label('Mots-clés de recherche')
                                    ->helperText('Mots que les clientes pourraient taper, invisibles sur la fiche. Ex. : lip gloss, brillant, repulpant.')
                                    ->maxLength(500),
                            ]),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Publication')
                        ->schema([
                            ToggleButtons::make('statut')
                                ->label('Statut')
                                ->options(Produit::statuts())
                                ->colors([
                                    Produit::BROUILLON => 'gray',
                                    Produit::PUBLIE => 'success',
                                    Produit::ARCHIVE => 'warning',
                                ])
                                ->inline()
                                ->default(Produit::BROUILLON)
                                ->required()
                                ->helperText('Publié : visible sur la boutique. Archivé : retiré de la vente, historique gardé.'),
                            Select::make('categorie_id')
                                ->label('Catégorie')
                                ->options(fn (): array => Categorie::query()->orderBy('position')->get()->mapWithKeys(fn (Categorie $categorie): array => [$categorie->id => $categorie->nom])->all())
                                ->required(),
                            Radio::make('nature')
                                ->label('Type')
                                ->options(Produit::natures())
                                ->default(Produit::NATURE_PRODUIT)
                                ->required(),
                            Select::make('gammes')
                                ->label('Collections')
                                ->relationship('gammes', 'nom')
                                ->getOptionLabelFromRecordUsing(fn (Gamme $gamme): string => $gamme->nom)
                                ->multiple()
                                ->preload(),
                            Toggle::make('edition_limitee')
                                ->label('Édition limitée')
                                ->helperText('Badge « Édition limitée » et page des éditions limitées.'),
                        ]),

                    Section::make('Prix')
                        ->schema([
                            TextInput::make('prix')
                                ->label('Prix de vente')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(10_000_000)
                                ->suffix('FCFA')
                                ->required()
                                ->helperText('Une couleur ou un format peut avoir son propre prix.'),
                            TextInput::make('cout_interne')
                                ->label('Coût interne')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->suffix('FCFA')
                                ->helperText('Facultatif, jamais montré aux clientes.')
                                ->visible(fn (): bool => (bool) auth('personnel')->user()?->can(Permissions::ACHATS_GERER)),
                            TextInput::make('seuil_alerte')
                                ->label('Seuil d’alerte de stock')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->default(5)
                                ->required()
                                ->helperText('À ce nombre ou en dessous : « Stock limité » sur la fiche et alerte au tableau de bord.'),
                        ]),

                    Section::make('Dates de vente')
                        ->description('Facultatives : pour une campagne ou une édition limitée. Hors de ces dates, le produit n’apparaît pas.')
                        ->collapsible()
                        ->schema([
                            DateTimePicker::make('campagne_debut_le')
                                ->label('En vente à partir du')
                                ->native(false)
                                ->displayFormat('d/m/Y H:i')
                                ->seconds(false),
                            DateTimePicker::make('campagne_fin_le')
                                ->label('Jusqu’au')
                                ->native(false)
                                ->displayFormat('d/m/Y H:i')
                                ->seconds(false)
                                ->minDate(fn (Get $get): mixed => $get('campagne_debut_le')),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    private static function variantes(): Repeater
    {
        return Repeater::make('variantes')
            ->hiddenLabel()
            ->relationship()
            ->orderColumn('position')
            ->minItems(1)
            ->defaultItems(1)
            ->addActionLabel('Ajouter une couleur ou un format')
            ->collapsible()
            // Une variante déjà mise en vente ne disparaît pas : on la retire de la vente.
            ->deletable(fn (?Produit $record): bool => $record?->publie_le === null)
            ->itemLabel(fn (array $state): ?string => filled($state['nom']['fr'] ?? null) ? $state['nom']['fr'] : null)
            ->columns(4)
            ->schema([
                ChampsTraduits::pour(fn (string $langue): array => [
                    TextInput::make("nom.{$langue}")
                        ->label('Couleur ou format')
                        ->placeholder('Ex. : Nude Glow')
                        ->maxLength(60),
                ])->columnSpan(2),
                ColorPicker::make('couleur')
                    ->label('Pastille')
                    ->hex(),
                TextInput::make('reference')
                    ->label('Référence')
                    ->placeholder('Automatique')
                    ->maxLength(60)
                    ->required(fn (?Variante $record): bool => $record !== null)
                    ->unique(Variante::class, 'reference', ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? mb_strtoupper(trim($state)) : null),
                TextInput::make('prix')
                    ->label('Prix propre (FCFA)')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->placeholder('Du produit'),
                TextInput::make('stock')
                    ->label(fn (?Variante $record): string => $record ? 'Stock' : 'Stock de départ')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    // Ensuite, le stock change par « Ajuster le stock » : chaque mouvement est noté.
                    ->disabled(fn (?Variante $record): bool => $record !== null)
                    ->dehydrated(fn (?Variante $record): bool => $record === null),
                Toggle::make('active')
                    ->label('En vente')
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(2),
                SpatieMediaLibraryFileUpload::make('photo')
                    ->label('Photo de cette couleur (facultative)')
                    ->collection(Variante::PHOTO)
                    ->image()
                    ->imagePreviewHeight('80')
                    ->columnSpanFull(),
            ]);
    }
}
