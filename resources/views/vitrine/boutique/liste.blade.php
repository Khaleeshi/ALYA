{{--
    Liste de produits (cahier §5.2) : boutique, catégorie, éditions limitées,
    coffrets. Un seul formulaire GET porte la recherche, le tri et les
    filtres : la page marche sans JavaScript ; avec, un changement de filtre
    sur grand écran ou de tri relance la recherche aussitôt. Sur téléphone,
    les filtres s'ouvrent en plein écran.
--}}
<x-layouts.vitrine :titre="$titre" :description="$sousTitre">
    <x-amani.bandeau-page :titre="$titre" :sous-titre="$sousTitre" :photo="$photo" />

    <div class="conteneur py-8 lg:py-12" x-data="{ filtres: false }">
        @if ($gammes->isNotEmpty())
            <section class="mb-12" aria-labelledby="titre-editions">
                <x-amani.titre-section :titre="__('vitrine.editions.collections')" id="titre-editions" />
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    @foreach ($gammes as $gamme)
                        <x-amani.carte-categorie
                            :titre="$gamme->nom"
                            :texte="$gamme->accroche"
                            :url="route_langue('collections.gamme', ['gamme' => $gamme->slug])"
                            :photo="$gamme->getFirstMedia(\App\Models\Gamme::COUVERTURE)"
                            :badge="__('vitrine.produit.edition_limitee')"
                            class="min-h-56"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($categories->isNotEmpty())
            <nav class="-mx-4 mb-8 overflow-x-auto px-4" aria-label="{{ __('vitrine.boutique.categories') }}">
                <ul class="flex gap-2">
                    @foreach ([null, ...$categories] as $categorie)
                        @php($active = $categorie?->is($categorieActive) ?? $categorieActive === null)
                        <li>
                            <a
                                href="{{ $categorie ? route_langue('boutique.categorie', ['categorie' => $categorie->slug]) : route_langue('boutique') }}"
                                @class([
                                    'inline-flex h-10 items-center rounded-full border px-5 text-[15px] whitespace-nowrap transition-colors',
                                    'border-bronze bg-nude-pale font-medium text-brun' => $active,
                                    'border-filet bg-blanc text-brun hover:border-nude' => ! $active,
                                ])
                                @if ($active) aria-current="page" @endif
                            >{{ $categorie?->nom ?? __('vitrine.boutique.tout') }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif

        @if ($titreGrille)
            <x-amani.titre-section :titre="$titreGrille" class="mb-6" />
        @endif

        @if ($catalogueVide)
            <x-amani.notice>{{ __('vitrine.boutique.bientot') }}</x-amani.notice>
        @else
            <form
                method="get"
                action="{{ request()->url() }}"
                class="lg:grid lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-10"
                x-on:change="if ($event.target.name === 'tri' || ($event.target.name !== 'q' && window.matchMedia('(min-width: 64rem)').matches)) $el.requestSubmit()"
                {{-- Adresse courte : sans les champs vides ni le tri par défaut. --}}
                x-on:submit="$el.querySelectorAll('input[name], select[name]').forEach(c => { if (c.value === '' || (c.name === 'tri' && c.value === 'nouveautes')) c.disabled = true })"
            >
                {{-- Filtres : colonne sur grand écran, plein écran sur téléphone --}}
                <aside
                    id="filtres-boutique"
                    class="max-lg:hidden"
                    x-bind:class="filtres ? 'max-lg:flex! max-lg:flex-col max-lg:fixed max-lg:inset-0 max-lg:z-50 max-lg:overflow-y-auto max-lg:bg-page max-lg:px-5 max-lg:pb-6' : ''"
                    x-trap.inert.noscroll="filtres"
                    x-on:keydown.escape.window="filtres = false"
                    aria-label="{{ __('vitrine.boutique.filtres') }}"
                >
                    <div class="sticky top-0 z-10 -mx-5 mb-2 flex items-center justify-between border-b border-filet bg-page px-5 py-4 lg:hidden">
                        <p class="titre text-2xl text-brun">{{ __('vitrine.boutique.filtres') }}</p>
                        <button type="button" x-on:click="filtres = false" class="inline-flex size-10 items-center justify-center rounded-full text-brun hover:bg-nude-pale" aria-label="{{ __('vitrine.fermer') }}">
                            <x-amani.icone nom="fermer" />
                        </button>
                    </div>

                    <div class="flex flex-col divide-y divide-filet">
                        @if ($options['tranches'] !== [])
                            <fieldset class="py-5 first:pt-0">
                                <legend class="float-left mb-3 w-full text-[15px] font-semibold text-brun">{{ __('vitrine.boutique.prix') }}</legend>
                                <div class="clear-left flex flex-col gap-2.5">
                                    @foreach ([null, ...$options['tranches']] as $tranche)
                                        <label class="flex cursor-pointer items-center gap-3 text-sm text-brun">
                                            <input type="radio" name="prix" value="{{ $tranche }}" @checked($filtres->prix === $tranche) class="size-[18px] shrink-0 cursor-pointer appearance-none rounded-full border border-taupe bg-blanc transition checked:border-[5px] checked:border-bronze">
                                            {{ $tranche ? __('vitrine.boutique.tranches.'.$tranche) : __('vitrine.boutique.tous_les_prix') }}
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        <fieldset class="py-5 first:pt-0">
                            <legend class="float-left mb-3 w-full text-[15px] font-semibold text-brun">{{ __('vitrine.boutique.disponibilite') }}</legend>
                            <x-amani.case nom="dispo" id="filtre-dispo" :libelle="__('vitrine.boutique.en_stock')" :coche="$filtres->enStock" class="clear-left" />
                        </fieldset>

                        @if ($options['types'])
                            <fieldset class="py-5">
                                <legend class="float-left mb-3 w-full text-[15px] font-semibold text-brun">{{ __('vitrine.boutique.type') }}</legend>
                                <div class="clear-left flex flex-col gap-2.5">
                                    @foreach ([null, ...\App\Support\Catalogue\FiltresBoutique::TYPES] as $type)
                                        <label class="flex cursor-pointer items-center gap-3 text-sm text-brun">
                                            <input type="radio" name="type" value="{{ $type }}" @checked($filtres->type === $type) class="size-[18px] shrink-0 cursor-pointer appearance-none rounded-full border border-taupe bg-blanc transition checked:border-[5px] checked:border-bronze">
                                            {{ __('vitrine.boutique.types.'.($type ?? 'tous')) }}
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        @if ($options['effets']->isNotEmpty())
                            <fieldset class="py-5">
                                <legend class="float-left mb-3 w-full text-[15px] font-semibold text-brun">{{ __('vitrine.boutique.effets') }}</legend>
                                <div class="clear-left flex flex-col gap-2.5">
                                    @foreach ($options['effets'] as $effet)
                                        <x-amani.case nom="effet[]" :id="'filtre-effet-'.$effet->slug" :valeur="$effet->slug" :libelle="$effet->nom" :coche="in_array($effet->slug, $filtres->effets, true)" />
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        @if ($options['ingredients']->isNotEmpty())
                            <fieldset class="py-5">
                                <legend class="float-left mb-3 w-full text-[15px] font-semibold text-brun">{{ __('vitrine.boutique.ingredients') }}</legend>
                                <div class="clear-left flex flex-col gap-2.5">
                                    @foreach ($options['ingredients'] as $ingredient)
                                        <x-amani.case nom="ingredient[]" :id="'filtre-ingredient-'.$ingredient->slug" :valeur="$ingredient->slug" :libelle="$ingredient->nom" :coche="in_array($ingredient->slug, $filtres->ingredients, true)" />
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif
                    </div>

                    {{-- Sur téléphone, ou sans JavaScript, les filtres s'appliquent ici (sur grand écran, ils s'appliquent seuls). --}}
                    <div class="mt-4 max-lg:sticky max-lg:bottom-0 max-lg:-mx-5 max-lg:mt-auto max-lg:border-t max-lg:border-filet max-lg:bg-page max-lg:px-5 max-lg:pt-4" x-bind:class="'lg:hidden'">
                        <x-amani.bouton :pleine-largeur="true" x-on:click="filtres = false">{{ __('vitrine.boutique.appliquer') }}</x-amani.bouton>
                    </div>
                </aside>

                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative min-w-0 flex-1 basis-64">
                            <label for="recherche-boutique" class="sr-only">{{ __('vitrine.boutique.rechercher') }}</label>
                            <input
                                id="recherche-boutique"
                                type="search"
                                name="q"
                                value="{{ $filtres->recherche }}"
                                maxlength="100"
                                placeholder="{{ __('vitrine.boutique.rechercher') }}"
                                class="h-12 w-full rounded-full border border-filet bg-blanc pr-12 pl-5 text-base text-brun placeholder:text-taupe/70 hover:border-nude focus:border-bronze focus:ring-2 focus:ring-nude-pale focus:outline-none"
                            >
                            <button type="submit" class="absolute top-1 right-1 inline-flex size-10 items-center justify-center rounded-full text-brun hover:bg-nude-pale" aria-label="{{ __('vitrine.rechercher') }}">
                                <x-amani.icone nom="recherche" :taille="20" />
                            </button>
                        </div>

                        <button type="button" x-on:click="filtres = true" class="inline-flex h-12 items-center gap-2 rounded-full border border-filet bg-blanc px-5 text-[15px] text-brun hover:border-nude lg:hidden" aria-controls="filtres-boutique" x-bind:aria-expanded="filtres">
                            <x-amani.icone nom="filtres" :taille="20" />
                            {{ __('vitrine.boutique.filtres') }}
                        </button>

                        <div class="flex items-center gap-2">
                            <label for="tri-boutique" class="text-sm whitespace-nowrap text-taupe">{{ __('vitrine.boutique.trier') }}</label>
                            <select id="tri-boutique" name="tri" class="h-12 rounded-full border border-filet bg-blanc pr-10 pl-4 text-[15px] text-brun hover:border-nude focus:border-bronze focus:ring-2 focus:ring-nude-pale focus:outline-none">
                                @foreach (\App\Support\Catalogue\FiltresBoutique::TRIS as $tri)
                                    <option value="{{ $tri }}" @selected($filtres->tri === $tri)>{{ __('vitrine.boutique.tris.'.$tri) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-2" aria-live="polite">
                        <p class="mr-2 text-sm text-taupe">
                            @if ($filtres->recherche !== '')
                                {{ __('vitrine.boutique.recherche_pour', ['recherche' => $filtres->recherche]) }} ·
                            @endif
                            {{ trans_choice('vitrine.boutique.resultats', $produits->total(), ['nombre' => $produits->total()]) }}
                        </p>

                        @if ($puces !== [])
                            <h2 class="sr-only">{{ __('vitrine.boutique.filtres_actifs') }}</h2>
                            @foreach ($puces as $puce)
                                <a href="{{ $puce['url'] }}" class="inline-flex h-8 items-center gap-1.5 rounded-full bg-nude-pale pr-2.5 pl-3.5 text-sm text-brun hover:bg-nude" aria-label="{{ __('vitrine.boutique.retirer_filtre', ['filtre' => $puce['libelle']]) }}">
                                    {{ $puce['libelle'] }}
                                    <x-amani.icone nom="fermer" :taille="14" />
                                </a>
                            @endforeach
                            <a href="{{ request()->url() }}" class="ml-1 text-sm text-brun underline decoration-or underline-offset-4 hover:text-bronze-fonce">{{ __('vitrine.boutique.effacer') }}</a>
                        @endif
                    </div>

                    @if ($produits->isEmpty())
                        <div class="mt-8 rounded-carte bg-nude-pale/60 px-6 py-12 text-center">
                            <p class="text-brun">{{ __('vitrine.boutique.aucun') }}</p>
                            <a href="{{ request()->url() }}" class="mt-3 inline-block text-sm text-brun underline decoration-or underline-offset-4">{{ __('vitrine.boutique.effacer') }}</a>
                        </div>
                    @else
                        <ul class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 xl:grid-cols-4">
                            @foreach ($produits as $produit)
                                <li class="flex">
                                    <x-amani.carte-produit :produit="$produit" class="w-full" />
                                </li>
                            @endforeach
                        </ul>

                        <x-amani.pagination :pages="$produits" class="mt-10" />
                    @endif
                </div>
            </form>
        @endif
    </div>
</x-layouts.vitrine>
