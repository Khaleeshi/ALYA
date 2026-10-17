{{--
    Prix en Bodoni, chiffres tabulaires. En promotion, le prix initial barré
    est à gauche (audit 9.3, CDC §28 : la réduction se lit ligne par ligne).
--}}
@props(['montant', 'barre' => null])

@php
    $enPromotion = $barre !== null && $barre > $montant;
@endphp

<span {{ $attributes->class('chiffres inline-flex flex-wrap items-baseline gap-x-3') }}>
    @if ($enPromotion)
        <s class="text-gris">
            <span class="sr-only">{{ __('vitrine.produit.prix_initial') }} :</span>
            {{ \App\Support\Montant::fcfa($barre) }}
        </s>
    @endif
    <span class="text-ivoire">
        @if ($enPromotion)
            <span class="sr-only">{{ __('vitrine.produit.prix_actuel') }} :</span>
        @endif
        {{ \App\Support\Montant::fcfa($montant) }}
    </span>
</span>
