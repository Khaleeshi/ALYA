{{--
    Ligne du panier (maquette 4) : photo, nom, couleur, disponibilité, prix,
    quantité en pilule, favori et suppression. Présentation seule : le
    comportement arrive avec le panier.
--}}
@props([
    'nom',
    'quantite',
    'prix',
    'prixBarre' => null,
    'teinte' => null,
    'photo' => null,
    'disponible' => true,
])

<li {{ $attributes->class('flex gap-4 border-b border-filet py-5 first:pt-0 last:border-b-0') }}>
    <div class="size-20 shrink-0 overflow-hidden rounded-champ bg-nude-pale sm:size-24">
        @if ($photo)
            <x-amani.image :media="$photo" alt="" sizes="96px" class="size-full object-cover" />
        @endif
    </div>

    <div class="flex min-w-0 flex-1 flex-col gap-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-[15px] font-medium text-brun">{{ $nom }}</p>
                @if ($teinte)
                    <p class="text-sm text-taupe">{{ __('vitrine.panier.teinte') }} : {{ $teinte }}</p>
                @endif
                @if ($disponible)
                    <p class="mt-0.5 flex items-center gap-1.5 text-sm text-vert">
                        <span class="size-1.5 rounded-full bg-vert" aria-hidden="true"></span>{{ __('vitrine.panier.en_stock') }}
                    </p>
                @endif
            </div>
            <x-amani.prix :montant="$prix * $quantite" :barre="$prixBarre ? $prixBarre * $quantite : null" class="shrink-0 text-[15px]" />
        </div>

        <div class="mt-auto flex items-center justify-between pt-2">
            <div class="flex items-center rounded-full border border-filet bg-blanc" role="group" aria-label="{{ __('vitrine.panier.quantite') }}">
                <button type="button" class="inline-flex size-9 items-center justify-center text-brun hover:text-bronze-fonce" aria-label="{{ __('vitrine.panier.diminuer') }}">
                    <x-amani.icone nom="moins" :taille="16" />
                </button>
                <span class="chiffres w-7 text-center text-sm font-medium" aria-live="polite">{{ $quantite }}</span>
                <button type="button" class="inline-flex size-9 items-center justify-center text-brun hover:text-bronze-fonce" aria-label="{{ __('vitrine.panier.augmenter') }}">
                    <x-amani.icone nom="plus" :taille="16" />
                </button>
            </div>

            <div class="flex items-center gap-1">
                <button type="button" class="inline-flex size-9 items-center justify-center text-taupe hover:text-bronze-fonce" aria-label="{{ __('vitrine.ajouter_aux_favoris', ['nom' => $nom]) }}">
                    <x-amani.icone nom="coeur" :taille="20" />
                </button>
                <button type="button" class="inline-flex size-9 items-center justify-center text-taupe hover:text-rouge" aria-label="{{ __('vitrine.panier.retirer') }}">
                    <x-amani.icone nom="corbeille" :taille="20" />
                </button>
            </div>
        </div>
    </div>
</li>
