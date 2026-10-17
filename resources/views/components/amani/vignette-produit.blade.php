{{--
    Vignette produit (maquettes 1, 2 et 4) : carte blanche aux coins arrondis,
    photo sur fond nude, un badge au plus, cœur des favoris, nom, prix, note
    des avis vérifiés si elle existe, bouton « Ajouter au panier ».

    photo / photoSurvol : médias Spatie (null tant que la photo n'est pas livrée).
    badge : ['texte' => 'Best-seller', 'ton' => 'cacao'] ou null.
    aPartirDe : « À partir de » devant le prix quand les couleurs ou formats n'ont pas tous le même.
    note / avis : seulement avec de vrais avis (cahier §13).
    Le cœur des favoris n'apparaît qu'avec la page « Mes favoris ».
--}}
@props([
    'nom',
    'url',
    'prix',
    'prixBarre' => null,
    'aPartirDe' => false,
    'photo' => null,
    'photoSurvol' => null,
    'badge' => null,
    'note' => null,
    'avis' => null,
])

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-carte bg-blanc shadow-carte transition-shadow duration-300 ease-amani hover:shadow-survol') }}>
    <div class="relative aspect-[5/4] overflow-hidden bg-nude-pale">
        <a href="{{ $url }}" class="absolute inset-0" tabindex="-1" aria-hidden="true">
            @if ($photo)
                <x-amani.image :media="$photo" alt="" sizes="(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw" class="size-full object-cover" />
                @if ($photoSurvol)
                    <x-amani.image :media="$photoSurvol" alt="" sizes="(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw" class="absolute inset-0 size-full object-cover opacity-0 transition-opacity duration-300 ease-amani group-hover:opacity-100" />
                @endif
            @else
                <span class="flex size-full items-center justify-center bg-gradient-to-br from-nude-pale to-nude text-sm text-taupe">{{ __('vitrine.produit.photo_a_venir') }}</span>
            @endif
        </a>

        @if ($badge)
            <x-amani.badge :ton="$badge['ton'] ?? 'cacao'" class="absolute top-3 left-3">{{ $badge['texte'] }}</x-amani.badge>
        @endif

        @if (Route::has(app()->getLocale().'.mes-favoris'))
            <button
                type="button"
                class="absolute top-2.5 right-2.5 inline-flex size-9 items-center justify-center rounded-full bg-blanc/85 text-brun transition-colors hover:text-rouge"
                aria-label="{{ __('vitrine.ajouter_aux_favoris', ['nom' => $nom]) }}"
                aria-pressed="false"
            >
                <x-amani.icone nom="coeur" :taille="20" />
            </button>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-1.5 p-4">
        <h3 class="text-[15px] leading-snug font-medium text-brun">
            <a href="{{ $url }}" class="hover:text-bronze-fonce">{{ $nom }}</a>
        </h3>

        <p class="flex flex-wrap items-baseline gap-x-1.5">
            @if ($aPartirDe)
                <span class="text-[13px] text-taupe">{{ __('vitrine.produit.a_partir_de') }}</span>
            @endif
            <x-amani.prix :montant="$prix" :barre="$prixBarre" class="text-[17px]" />
        </p>

        @if ($note !== null)
            <x-amani.etoiles :note="$note" :avis="$avis" />
        @endif

        <div class="mt-auto pt-3">
            @isset($action)
                {{ $action }}
            @else
                <x-amani.bouton type="button" taille="petite" icone="panier" :pleine-largeur="true">{{ __('vitrine.produit.ajouter') }}</x-amani.bouton>
            @endisset
        </div>
    </div>
</article>
