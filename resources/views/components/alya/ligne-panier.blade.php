{{--
    Ligne du panier : vignette, nom, teinte, quantité par plus et moins, prix
    (barré ligne par ligne en cas de promotion, CDC §28), retrait.
    Présentation seule : le comportement arrive avec le panier (phase 3).
--}}
@props([
    'nom',
    'quantite',
    'prix',
    'prixBarre' => null,
    'teinte' => null,
    'photo' => null,
])

<li {{ $attributes->class('flex gap-4 border-b border-filet py-5 first:pt-0') }}>
    <div class="aspect-[4/5] w-20 shrink-0 bg-relief">
        @if ($photo)
            <x-alya.image :media="$photo" alt="" sizes="80px" class="size-full object-cover" />
        @endif
    </div>

    <div class="flex min-w-0 flex-1 flex-col gap-2">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="titre truncate text-base text-ivoire">{{ $nom }}</p>
                @if ($teinte)
                    <p class="text-sm text-gris">{{ __('vitrine.panier.teinte') }} : {{ $teinte }}</p>
                @endif
            </div>
            <x-alya.prix :montant="$prix * $quantite" :barre="$prixBarre ? $prixBarre * $quantite : null" class="shrink-0 text-sm" />
        </div>

        <div class="mt-auto flex items-center justify-between">
            <div class="flex items-center border border-filet" role="group" aria-label="{{ __('vitrine.panier.quantite') }}">
                <button type="button" class="inline-flex size-9 items-center justify-center text-ivoire hover:text-or" aria-label="{{ __('vitrine.panier.diminuer') }}">
                    <x-alya.icone nom="moins" :taille="16" />
                </button>
                <span class="chiffres w-8 text-center text-sm" aria-live="polite">{{ $quantite }}</span>
                <button type="button" class="inline-flex size-9 items-center justify-center text-ivoire hover:text-or" aria-label="{{ __('vitrine.panier.augmenter') }}">
                    <x-alya.icone nom="plus" :taille="16" />
                </button>
            </div>

            <x-alya.bouton variante="lien" type="button" class="text-[11px]">{{ __('vitrine.panier.retirer') }}</x-alya.bouton>
        </div>
    </div>
</li>
