{{--
    En-tête de la vitrine (cahier §4.1, maquettes) : logo, menu principal
    (sept entrées au plus, la page en cours en pastille nude), icônes recherche,
    compte, favoris et panier avec compteur.

    liens : liste de ['libelle' => …, 'url' => …, 'actif' => bool].
    La recherche et les favoris n'apparaissent que lorsque leurs pages existent.
--}}
@props([
    'liens' => [],
    'nombreArticles' => 0,
])

@use('App\Support\Langues')

@php
    if (count($liens) > 7) {
        throw new InvalidArgumentException('Le menu principal compte sept entrées au plus (cahier §4.1).');
    }

    $langue = app()->getLocale();
    $autreLangue = collect(Langues::ouvertes())->first(fn (string $l): bool => $l !== $langue);
    $routeExiste = fn (string $nom): bool => Route::has($langue.'.'.$nom);
    $compte = auth('web')->check() ? route_langue('mon-compte') : route_langue('connexion');
    $icone = 'inline-flex size-10 items-center justify-center rounded-full text-brun transition-colors hover:bg-nude-pale hover:text-bronze-fonce';
@endphp

<header x-data="{ menu: false }" {{ $attributes->class('sticky top-0 z-30 border-b border-filet bg-blanc/95 backdrop-blur') }}>
    <div class="conteneur flex h-[72px] items-center gap-4 lg:h-[88px] lg:gap-8">
        <button type="button" class="{{ $icone }} -ml-2 lg:hidden" x-on:click="menu = true" aria-controls="menu-mobile" x-bind:aria-expanded="menu" aria-label="{{ __('vitrine.ouvrir_menu') }}">
            <x-amani.icone nom="menu" />
        </button>

        <a href="{{ route_langue('accueil') }}" class="shrink-0" aria-label="{{ __('vitrine.marque') }}, {{ __('vitrine.accueil') }}">
            <x-amani.logo />
        </a>

        @if (count($liens) > 0)
            <nav class="hidden flex-1 justify-center lg:flex" aria-label="{{ __('vitrine.navigation_principale') }}">
                <ul class="flex items-center gap-1 xl:gap-2">
                    @foreach ($liens as $lien)
                        <li>
                            <a
                                href="{{ $lien['url'] }}"
                                @class([
                                    'rounded-full px-4 py-2 text-[15px] whitespace-nowrap transition-colors',
                                    'bg-nude-pale font-medium text-brun' => $lien['actif'] ?? false,
                                    'text-brun hover:bg-nude-pale/60' => ! ($lien['actif'] ?? false),
                                ])
                                @if ($lien['actif'] ?? false) aria-current="page" @endif
                            >{{ $lien['libelle'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif

        <div class="ml-auto flex items-center gap-0.5 sm:gap-1">
            @if ($autreLangue)
                <a href="{{ Langues::pageEquivalente($autreLangue) }}" hreflang="{{ $autreLangue }}" lang="{{ $autreLangue }}" class="mr-1 hidden text-sm font-medium text-taupe hover:text-brun sm:inline">
                    <span aria-hidden="true">{{ __('vitrine.autre_langue_court') }}</span><span class="sr-only">{{ __('vitrine.autre_langue') }}</span>
                </a>
            @endif

            @if ($routeExiste('recherche'))
                <a href="{{ route_langue('recherche') }}" class="{{ $icone }}" aria-label="{{ __('vitrine.rechercher') }}"><x-amani.icone nom="recherche" /></a>
            @endif

            <a href="{{ $compte }}" class="{{ $icone }}" aria-label="{{ __('vitrine.mon_compte') }}"><x-amani.icone nom="compte" /></a>

            @if ($routeExiste('favoris'))
                <a href="{{ route_langue('favoris') }}" class="{{ $icone }} hidden sm:inline-flex" aria-label="{{ __('vitrine.favoris') }}"><x-amani.icone nom="coeur" /></a>
            @endif

            {{-- Ouvre le tiroir du panier ; sans JavaScript, mène à la page du panier. --}}
            <a
                href="{{ $routeExiste('panier') ? route_langue('panier') : '#' }}"
                x-on:click.prevent="$dispatch('ouvrir-panier')"
                class="{{ $icone }} relative -mr-2"
                aria-label="{{ trans_choice('vitrine.panier.ouvrir', $nombreArticles, ['nombre' => $nombreArticles]) }}"
            >
                <x-amani.icone nom="sac" />
                <span class="chiffres absolute top-0.5 right-0.5 flex size-[18px] items-center justify-center rounded-full bg-cacao text-[10px] font-semibold text-blanc" aria-hidden="true">{{ $nombreArticles }}</span>
            </a>
        </div>
    </div>

    {{-- Menu sur téléphone --}}
    <div
        id="menu-mobile"
        x-show="menu"
        x-cloak
        x-trap.inert.noscroll="menu"
        x-on:keydown.escape.window="menu = false"
        x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex flex-col bg-page lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('vitrine.navigation_principale') }}"
    >
        <div class="conteneur flex h-[72px] items-center justify-between border-b border-filet">
            <x-amani.logo taille="petite" />
            <button type="button" x-on:click="menu = false" class="{{ $icone }} -mr-2" aria-label="{{ __('vitrine.fermer_menu') }}">
                <x-amani.icone nom="fermer" />
            </button>
        </div>
        <nav class="conteneur flex flex-1 flex-col justify-between py-8">
            <ul class="flex flex-col gap-1">
                @foreach ($liens as $lien)
                    <li>
                        <a href="{{ $lien['url'] }}" @class(['block rounded-champ px-4 py-3 text-lg text-brun', 'bg-nude-pale font-medium' => $lien['actif'] ?? false])>{{ $lien['libelle'] }}</a>
                    </li>
                @endforeach
            </ul>
            @if ($autreLangue)
                <a href="{{ Langues::pageEquivalente($autreLangue) }}" hreflang="{{ $autreLangue }}" lang="{{ $autreLangue }}" class="px-4 text-sm font-medium text-taupe">{{ __('vitrine.autre_langue') }}</a>
            @endif
        </nav>
    </div>
</header>
