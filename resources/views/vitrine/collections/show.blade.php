{{-- Une collection : sa présentation, puis ses produits dans l'ordre choisi. --}}
@use('App\Models\Gamme')

<x-layouts.vitrine :titre="$gamme->nom" :description="$gamme->accroche">
    <x-amani.bandeau-page :titre="$gamme->nom" :sous-titre="$gamme->accroche" :photo="$gamme->getFirstMedia(Gamme::COUVERTURE)" />

    <div class="conteneur py-8 lg:py-12">
        <x-amani.fil-ariane :liens="[
            ['libelle' => __('vitrine.accueil'), 'url' => route_langue('accueil')],
            ['libelle' => __('vitrine.navigation.collections'), 'url' => route_langue('collections')],
            ['libelle' => $gamme->nom, 'url' => route_langue('collections.gamme', ['gamme' => $gamme->slug])],
        ]" />

        @if ($gamme->description)
            <p class="mt-6 max-w-3xl text-lg leading-relaxed text-brun/85">{!! nl2br(e($gamme->description)) !!}</p>
        @endif

        <h2 class="sr-only">{{ __('vitrine.collections.produits') }}</h2>

        @if ($produits->isEmpty())
            <x-amani.notice class="mt-8">{{ __('vitrine.boutique.bientot') }}</x-amani.notice>
        @else
            <ul class="mt-8 grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($produits as $produit)
                    <li class="flex"><x-amani.carte-produit :produit="$produit" class="w-full" /></li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.vitrine>
