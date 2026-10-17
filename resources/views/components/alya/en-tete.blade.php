{{--
    En-tête de la vitrine (audit 9.1 et 9.3) : sept entrées au plus, sans
    icône, en capitales espacées ; à droite les seules icônes fonctionnelles
    (recherche, compte, favoris, sac) et le choix de la langue.

    liens : liste de ['libelle' => …, 'url' => …], sept au plus.
    La recherche et les favoris n'apparaissent que lorsque leurs pages existent.
    Le mot « ALYA » en Bodoni tient lieu de logo jusqu'à la livraison du logo vectoriel.
--}}
@props([
    'liens' => [],
    'nombreArticles' => 0,
])

@use('App\Support\Langues')

@php
    if (count($liens) > 7) {
        throw new InvalidArgumentException('La navigation principale compte sept entrées au plus (audit 9.1).');
    }

    $langue = app()->getLocale();
    $autreLangue = $langue === 'fr' ? 'en' : 'fr';
    $routeExiste = fn (string $nom): bool => Route::has($langue.'.'.$nom);
    $compte = auth('web')->check() ? route_langue('mon-compte') : route_langue('connexion');
@endphp

<header x-data="{ menu: false }" {{ $attributes->class('sticky top-0 z-30 border-b border-filet bg-noir') }}>
    <div class="conteneur grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-6 lg:h-20">
        <div class="flex items-center">
            <button
                type="button"
                class="-ml-2 inline-flex size-10 items-center justify-center text-ivoire lg:hidden"
                x-on:click="menu = true"
                aria-controls="menu-mobile"
                x-bind:aria-expanded="menu"
                aria-label="{{ __('vitrine.ouvrir_menu') }}"
            >
                <x-alya.icone nom="menu" />
            </button>

            <a
                href="{{ Langues::pageEquivalente($autreLangue) }}"
                hreflang="{{ $autreLangue }}"
                lang="{{ $autreLangue }}"
                class="etiquette hidden text-gris transition-colors hover:text-ivoire lg:inline"
            ><span aria-hidden="true">{{ __('vitrine.autre_langue_court') }}</span><span class="sr-only">{{ __('vitrine.autre_langue') }}</span></a>
        </div>

        <a href="{{ route_langue('accueil') }}" class="titre-page text-[22px] tracking-[0.32em] text-ivoire lg:text-[28px]">
            ALYA<span class="sr-only"> Cosmetic, {{ __('vitrine.accueil') }}</span>
        </a>

        <div class="flex items-center justify-end gap-1 sm:gap-2">
            @if ($routeExiste('recherche'))
                <a href="{{ route_langue('recherche') }}" class="inline-flex size-10 items-center justify-center text-ivoire hover:text-or" aria-label="{{ __('vitrine.rechercher') }}"><x-alya.icone nom="recherche" /></a>
            @endif

            <a href="{{ $compte }}" class="inline-flex size-10 items-center justify-center text-ivoire hover:text-or" aria-label="{{ __('vitrine.mon_compte') }}"><x-alya.icone nom="compte" /></a>

            @if ($routeExiste('favoris'))
                <a href="{{ route_langue('favoris') }}" class="hidden size-10 items-center justify-center text-ivoire hover:text-or sm:inline-flex" aria-label="{{ __('vitrine.favoris') }}"><x-alya.icone nom="coeur" /></a>
            @endif

            <button
                type="button"
                x-on:click="$dispatch('ouvrir-panier')"
                class="relative -mr-2 inline-flex size-10 items-center justify-center text-ivoire hover:text-or"
                aria-label="{{ trans_choice('vitrine.panier.ouvrir', $nombreArticles, ['nombre' => $nombreArticles]) }}"
            >
                <x-alya.icone nom="sac" />
                @if ($nombreArticles > 0)
                    <span class="chiffres absolute top-1 right-0 min-w-4 bg-or px-1 text-center text-[10px] leading-4 text-noir" aria-hidden="true">{{ $nombreArticles }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- Sur ordinateur, la navigation occupe sa propre ligne, centrée sous la marque. --}}
    @if (count($liens) > 0)
        <nav class="hidden pb-5 lg:block" aria-label="{{ __('vitrine.navigation_principale') }}">
            <ul class="flex justify-center gap-10">
                @foreach ($liens as $lien)
                    <li><a href="{{ $lien['url'] }}" class="etiquette whitespace-nowrap text-ivoire transition-colors hover:text-or">{{ $lien['libelle'] }}</a></li>
                @endforeach
            </ul>
        </nav>
    @endif

    {{-- Menu sur téléphone --}}
    <div
        id="menu-mobile"
        x-show="menu"
        x-cloak
        x-trap.inert.noscroll="menu"
        x-on:keydown.escape.window="menu = false"
        x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex flex-col bg-noir lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('vitrine.navigation_principale') }}"
    >
        <div class="conteneur flex h-16 items-center justify-between border-b border-filet">
            <span class="titre-page text-[22px] tracking-[0.32em]">ALYA</span>
            <button type="button" x-on:click="menu = false" class="-mr-2 inline-flex size-10 items-center justify-center" aria-label="{{ __('vitrine.fermer_menu') }}">
                <x-alya.icone nom="fermer" />
            </button>
        </div>
        <nav class="conteneur flex flex-1 flex-col justify-between py-10">
            <ul class="flex flex-col gap-6">
                @foreach ($liens as $lien)
                    <li><a href="{{ $lien['url'] }}" class="titre-page text-2xl text-ivoire">{{ $lien['libelle'] }}</a></li>
                @endforeach
            </ul>
            <a href="{{ Langues::pageEquivalente($autreLangue) }}" hreflang="{{ $autreLangue }}" lang="{{ $autreLangue }}" class="etiquette text-gris">{{ __('vitrine.autre_langue') }}</a>
        </nav>
    </div>
</header>
