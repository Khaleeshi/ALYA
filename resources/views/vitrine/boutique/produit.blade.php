{{--
    Fiche produit (cahier §5.3, maquette 2) : galerie, nom, prix, choix de la
    couleur ou du format, disponibilité, atouts, puis description et
    bienfaits, ingrédients clés, conseils, produits similaires.

    Le choix d'une couleur met à jour le prix, la disponibilité et la photo
    (Alpine) ; sans JavaScript, la page reste complète et lisible.
    Les boutons « Ajouter au panier » et « Acheter maintenant » arrivent
    avec le panier. Les avis s'afficheront quand ils existeront (§13).
--}}
@use('App\Models\Produit')
@use('App\Models\Variante')
@use('App\Support\Montant')

@php
    $lignes = fn (?string $texte): array => array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $texte)), fn (string $ligne): bool => $ligne !== ''));
    $paragraphes = fn (?string $texte): array => array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $texte)), fn (string $p): bool => $p !== ''));

    // Galerie : les photos du produit, puis celles des couleurs qui en ont une.
    $images = $produit->getMedia(Produit::PHOTOS)->values()->all();
    $imageDeVariante = [];
    foreach ($produit->variantes as $variante) {
        if ($photo = $variante->photo()) {
            $imageDeVariante[$variante->id] = count($images);
            $images[] = $photo;
        }
    }
    $imageInitiale = $imageDeVariante[$choisie->id] ?? 0;

    $etats = [
        Variante::EN_STOCK => ['succes', __('vitrine.fiche.en_stock')],
        Variante::STOCK_LIMITE => ['attention', __('vitrine.fiche.stock_limite')],
        Variante::RUPTURE => ['alerte', __('vitrine.fiche.rupture')],
    ];

    $variantes = $produit->variantes->mapWithKeys(fn (Variante $variante): array => [$variante->id => [
        'nom' => (string) $variante->nom,
        'prix' => Montant::fcfa($variante->prixFinal()),
        'etat' => $variante->etatStock(),
        'image' => $imageDeVariante[$variante->id] ?? null,
    ]]);

    $avecChoix = $produit->variantes->count() > 1 || filled($choisie->nom);
    $avecPastilles = $produit->variantes->every(fn (Variante $variante): bool => $variante->couleurCss() !== null);

    $description = $paragraphes($produit->description);
    $bienfaits = $lignes($produit->bienfaits);
    $conseils = $lignes($produit->conseils);
    $avecIngredients = $produit->ingredients->isNotEmpty() || filled($produit->composition);
    $sections = array_filter([
        'description' => $description !== [] || $bienfaits !== [] ? __('vitrine.fiche.description') : null,
        'ingredients' => $avecIngredients ? __('vitrine.fiche.ingredients') : null,
        'conseils' => $conseils !== [] ? __('vitrine.fiche.conseils') : null,
    ]);

    $url = route_langue('boutique.produit', ['produit' => $produit->slug]);
    $donneesStructurees = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $produit->nom,
        'description' => $produit->accroche ?? $produit->nom,
        'image' => array_map(fn ($media) => $media->getUrl(), $images),
        'sku' => $choisie->reference,
        'brand' => ['@type' => 'Brand', 'name' => __('vitrine.marque')],
        'offers' => [
            '@type' => 'AggregateOffer',
            'priceCurrency' => 'XOF',
            'lowPrice' => $produit->prixMinimum(),
            'highPrice' => $produit->variantes->max(fn (Variante $variante): int => $variante->prixFinal()),
            'offerCount' => $produit->variantes->count(),
            'availability' => $produit->enStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => $url,
        ],
    ];
@endphp

<x-layouts.vitrine :titre="$produit->meta_titre ?: $produit->nom" :description="$produit->meta_description ?: $produit->accroche">
    <x-slot:tete>
        <link rel="canonical" href="{{ $url }}">
        <script type="application/ld+json">{!! json_encode($donneesStructurees, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    </x-slot:tete>

    <div class="conteneur pt-5 pb-12 lg:pt-8">
        <x-amani.fil-ariane :liens="[
            ['libelle' => __('vitrine.accueil'), 'url' => route_langue('accueil')],
            ['libelle' => $produit->categorie->nom, 'url' => route_langue('boutique.categorie', ['categorie' => $produit->categorie->slug])],
            ['libelle' => $produit->nom, 'url' => $url],
        ]" />

        <div
            class="mt-5 grid gap-8 lg:mt-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)] lg:gap-14"
            x-data="{ choisie: {{ $choisie->id }}, image: {{ $imageInitiale }}, variantes: @js($variantes) }"
            x-effect="if (variantes[choisie].image !== null) image = variantes[choisie].image"
        >
            {{-- Galerie --}}
            <section class="flex flex-col-reverse gap-3 sm:flex-row sm:gap-4" aria-label="{{ __('vitrine.fiche.galerie') }}">
                @if (count($images) > 1)
                    <ul class="flex gap-3 overflow-x-auto pb-1 sm:w-20 sm:shrink-0 sm:flex-col sm:overflow-visible sm:pb-0">
                        @foreach ($images as $rang => $media)
                            <li class="shrink-0">
                                <button
                                    type="button"
                                    x-on:click="image = {{ $rang }}"
                                    x-bind:aria-pressed="image === {{ $rang }}"
                                    @class(['block size-18 overflow-hidden rounded-champ border-2 bg-nude-pale transition-colors sm:size-20', 'border-bronze' => $rang === $imageInitiale, 'border-transparent' => $rang !== $imageInitiale])
                                    x-bind:class="image === {{ $rang }} ? 'border-bronze' : 'border-transparent'"
                                    aria-label="{{ __('vitrine.fiche.voir_photo', ['numero' => $rang + 1]) }}"
                                >
                                    <x-amani.image :media="$media" sizes="80px" class="size-full object-cover" />
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="relative aspect-square flex-1 overflow-hidden rounded-bloc bg-nude-pale">
                    @forelse ($images as $rang => $media)
                        <div class="absolute inset-0" x-show="image === {{ $rang }}" @if ($rang !== $imageInitiale) x-cloak @endif>
                            <x-amani.image :media="$media" :alt="$produit->nom" sizes="(min-width: 1024px) 45vw, 100vw" :prioritaire="$rang === $imageInitiale" class="size-full object-cover" />
                        </div>
                    @empty
                        <span class="flex size-full items-center justify-center bg-gradient-to-br from-nude-pale to-nude text-taupe">{{ __('vitrine.produit.photo_a_venir') }}</span>
                    @endforelse

                    @if ($produit->edition_limitee)
                        <x-amani.badge ton="bronze" class="absolute top-4 left-4">{{ __('vitrine.produit.edition_limitee') }}</x-amani.badge>
                    @elseif ($produit->estNouveau())
                        <x-amani.badge ton="bronze" class="absolute top-4 left-4">{{ __('vitrine.produit.nouveau') }}</x-amani.badge>
                    @endif
                </div>
            </section>

            {{-- Informations et choix --}}
            <div>
                <p class="sur-titre text-taupe">{{ $produit->categorie->nom }}</p>
                <h1 class="titre mt-2 text-[34px] leading-tight text-brun lg:text-[44px]">{{ $produit->nom }}</h1>

                <p class="chiffres mt-4 text-[26px] font-semibold text-brun">
                    <span x-text="variantes[choisie].prix">{{ Montant::fcfa($choisie->prixFinal()) }}</span>
                </p>

                @if ($produit->accroche)
                    <p class="mt-4 leading-relaxed text-brun/85">{{ $produit->accroche }}</p>
                @endif

                @if ($avecChoix)
                    <fieldset class="mt-7">
                        <legend class="text-[15px] font-medium text-brun">
                            {{ $avecPastilles ? __('vitrine.fiche.couleur') : __('vitrine.fiche.format') }} :
                            <span class="font-normal" x-text="variantes[choisie].nom">{{ $choisie->nom }}</span>
                        </legend>
                        <div class="mt-3 flex flex-wrap gap-3">
                            @foreach ($produit->variantes as $variante)
                                <label class="relative cursor-pointer" @if ($avecPastilles) title="{{ $variante->nom }}" @endif>
                                    <input type="radio" name="variante" value="{{ $variante->id }}" x-model.number="choisie" @checked($variante->is($choisie)) class="peer sr-only">
                                    @if ($avecPastilles)
                                        <span
                                            @class(['block size-10 rounded-full border-[3px] border-blanc ring-1 ring-filet transition peer-checked:ring-2 peer-checked:ring-bronze peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-bronze', 'opacity-45' => $variante->stock <= 0])
                                            style="background-color: {{ $variante->couleurCss() }}"
                                        ></span>
                                        <span class="sr-only">{{ $variante->nom }}@if ($variante->stock <= 0), {{ __('vitrine.fiche.rupture') }}@endif</span>
                                        @if ($variante->stock <= 0)
                                            <span class="pointer-events-none absolute inset-0 m-auto h-px w-9 -rotate-45 bg-brun/60" aria-hidden="true"></span>
                                        @endif
                                    @else
                                        <span @class(['inline-flex h-10 items-center rounded-full border border-filet bg-blanc px-4 text-sm text-brun transition-colors peer-checked:border-bronze peer-checked:bg-nude-pale peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-bronze', 'text-taupe line-through' => $variante->stock <= 0])>{{ $variante->nom ?: $produit->nom }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <p class="mt-6" aria-live="polite">
                    {{-- Sans JavaScript, seul l'état de la variante affichée reste visible (x-cloak). --}}
                    @foreach ($etats as $etat => [$ton, $texte])
                        <span x-show="variantes[choisie].etat === '{{ $etat }}'" @if ($etat !== $choisie->etatStock()) x-cloak @endif>
                            <x-amani.pastille-etat :etat="$ton">{{ $texte }}</x-amani.pastille-etat>
                        </span>
                    @endforeach
                </p>

                @if ($produit->atouts->isNotEmpty())
                    <ul class="mt-8 grid grid-cols-2 gap-4 border-t border-filet pt-7 sm:grid-cols-4">
                        @foreach ($produit->atouts as $atout)
                            <li class="flex flex-col items-center gap-2.5 text-center">
                                <span class="inline-flex size-14 items-center justify-center rounded-full border border-nude text-bronze">
                                    <x-amani.icone :nom="$atout->icone" :taille="26" />
                                </span>
                                <span class="text-[13px] leading-snug text-brun">{{ $atout->texte }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    @if (count($sections) > 1)
        <nav class="border-y border-filet bg-blanc" aria-label="{{ __('vitrine.fiche.sections') }}">
            <ul class="conteneur flex gap-6 overflow-x-auto">
                @foreach ($sections as $ancre => $libelle)
                    <li><a href="#{{ $ancre }}" class="inline-flex h-14 items-center border-b-2 border-transparent text-[15px] whitespace-nowrap text-brun hover:border-or">{{ $libelle }}</a></li>
                @endforeach
            </ul>
        </nav>
    @endif

    @if (isset($sections['description']))
        <section id="description" @class([
            'conteneur grid scroll-mt-28 gap-8 py-12 lg:gap-10',
            'lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_minmax(0,0.9fr)]' => isset($images[1]),
            'lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]' => ! isset($images[1]),
        ])>
            <div>
                <h2 class="titre text-[28px] text-brun">{{ __('vitrine.fiche.description') }}</h2>
                <div class="mt-4 flex flex-col gap-4 leading-relaxed text-brun/85">
                    @foreach ($description as $paragraphe)
                        <p>{!! nl2br(e($paragraphe)) !!}</p>
                    @endforeach
                </div>
            </div>

            @if (isset($images[1]))
                <div class="hidden overflow-hidden rounded-carte bg-nude-pale lg:block">
                    <x-amani.image :media="$images[1]" alt="" sizes="30vw" class="size-full object-cover" />
                </div>
            @endif

            @if ($bienfaits !== [])
                <aside class="rounded-carte bg-nude-pale/70 p-6">
                    <h3 class="titre flex items-center gap-2 text-[22px] text-brun">
                        <x-amani.icone nom="eclat" :taille="20" class="text-bronze" />
                        {{ __('vitrine.fiche.bienfaits') }}
                    </h3>
                    <ul class="mt-4 flex flex-col gap-3">
                        @foreach ($bienfaits as $bienfait)
                            <li class="flex items-start gap-3 text-[15px] text-brun">
                                <span class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-bronze text-blanc"><x-amani.icone nom="coche" :taille="13" /></span>
                                {{ $bienfait }}
                            </li>
                        @endforeach
                    </ul>
                </aside>
            @endif
        </section>
    @endif

    @if ($avecIngredients)
        <section id="ingredients" class="conteneur scroll-mt-28 py-10">
            <x-amani.titre-section :titre="__('vitrine.fiche.ingredients_cles')" />

            @if ($produit->ingredients->isNotEmpty())
                <ul class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($produit->ingredients as $ingredient)
                        <li class="flex flex-col items-center gap-2 text-center">
                            <span class="block size-28 overflow-hidden rounded-full bg-gradient-to-br from-nude-pale to-nude shadow-carte">
                                @if ($photo = $ingredient->photo())
                                    <x-amani.image :media="$photo" alt="" sizes="112px" class="size-full object-cover" />
                                @else
                                    <span class="flex size-full items-center justify-center text-bronze"><x-amani.icone nom="feuille" :taille="32" /></span>
                                @endif
                            </span>
                            <span class="mt-1 text-[15px] font-semibold text-brun">{{ $ingredient->nom }}</span>
                            @if ($ingredient->bienfait)
                                <span class="text-[13px] text-taupe">{{ $ingredient->bienfait }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($produit->composition)
                <p class="mt-8 max-w-3xl text-sm leading-relaxed text-taupe">
                    <span class="font-semibold text-brun">{{ __('vitrine.fiche.composition') }} :</span> {{ $produit->composition }}
                </p>
            @endif
        </section>
    @endif

    @if ($conseils !== [])
        <section id="conseils" class="conteneur scroll-mt-28 py-10">
            <x-amani.titre-section :titre="__('vitrine.fiche.conseils')" />
            <ol class="mt-6 flex max-w-3xl flex-col gap-3">
                @foreach ($conseils as $conseil)
                    <li class="flex items-start gap-4 text-brun">
                        <span class="chiffres inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-nude-pale text-sm font-semibold text-bronze-fonce">{{ $loop->iteration }}</span>
                        <span class="pt-0.5">{{ $conseil }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    @if ($similaires->isNotEmpty())
        <section class="conteneur py-12">
            <x-amani.titre-section :titre="__('vitrine.fiche.similaires')" :lien="route_langue('boutique.categorie', ['categorie' => $produit->categorie->slug])" />
            <ul class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($similaires as $similaire)
                    <li class="flex"><x-amani.carte-produit :produit="$similaire" class="w-full" /></li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.vitrine>
