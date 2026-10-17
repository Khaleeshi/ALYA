{{--
    Vignette produit (audit 9.3) : photo 4:5 sur noir, nom, une ligne de
    descriptif, prix, teintes en pastilles de 10 px, action « Ajouter » au
    survol sur ordinateur et toujours visible sur téléphone. Un badge au plus.
    Au survol, la photo de texture remplace le packshot en 300 ms.

    photo / photoSurvol : médias Spatie (null tant que la photo n'est pas livrée).
    teintes : liste de ['nom' => 'Diamond Rose', 'couleur' => '#C98C8C'].
--}}
@props([
    'nom',
    'url',
    'prix',
    'descriptif' => null,
    'prixBarre' => null,
    'photo' => null,
    'photoSurvol' => null,
    'teintes' => [],
    'badge' => null,
])

<article {{ $attributes->class('group relative flex flex-col') }}>
    <div class="relative aspect-[4/5] overflow-hidden bg-surface">
        <a href="{{ $url }}" class="absolute inset-0" tabindex="-1" aria-hidden="true">
            @if ($photo)
                <x-alya.image
                    :media="$photo"
                    alt=""
                    sizes="(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw"
                    class="size-full object-cover"
                />
                @if ($photoSurvol)
                    <x-alya.image
                        :media="$photoSurvol"
                        alt=""
                        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw"
                        class="absolute inset-0 size-full object-cover opacity-0 transition-opacity duration-300 ease-alya group-hover:opacity-100"
                    />
                @endif
            @else
                <span class="etiquette flex size-full items-center justify-center p-6 text-center text-gris">{{ __('vitrine.produit.photo_a_venir') }}</span>
            @endif
        </a>

        @if ($badge)
            <x-alya.badge class="absolute top-3 left-3">{{ $badge }}</x-alya.badge>
        @endif

        <button
            type="button"
            class="absolute top-2 right-2 inline-flex size-10 items-center justify-center text-ivoire transition-colors hover:text-or"
            aria-label="{{ __('vitrine.ajouter_aux_favoris', ['nom' => $nom]) }}"
            aria-pressed="false"
        >
            <x-alya.icone nom="coeur" />
        </button>

        <div class="absolute inset-x-0 bottom-0 p-3 transition-opacity duration-300 ease-alya lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100">
            @isset($action)
                {{ $action }}
            @else
                <x-alya.bouton variante="secondaire" type="button" :pleine-largeur="true" class="bg-noir/85">
                    {{ __('vitrine.produit.ajouter') }}
                </x-alya.bouton>
            @endisset
        </div>
    </div>

    <div class="flex flex-col gap-1.5 pt-4">
        <h3 class="titre text-lg text-ivoire">
            <a href="{{ $url }}" class="hover:text-or-clair">{{ $nom }}</a>
        </h3>

        @if ($descriptif)
            <p class="line-clamp-1 text-sm text-gris">{{ $descriptif }}</p>
        @endif

        <x-alya.prix :montant="$prix" :barre="$prixBarre" class="text-base" />

        @if (count($teintes) > 0)
            <ul class="mt-1 flex flex-wrap gap-1.5" aria-label="{{ __('vitrine.produit.teintes') }}">
                @foreach ($teintes as $teinte)
                    <li>
                        <span class="block size-2.5 border border-filet" style="background-color: {{ $teinte['couleur'] }}" title="{{ $teinte['nom'] }}"></span>
                        <span class="sr-only">{{ $teinte['nom'] }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</article>
