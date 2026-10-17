{{--
    Prix en FCFA (Jost demi-gras). En promotion, le prix initial barré est à
    côté : la réduction se lit ligne par ligne. ton « bronze » pour les
    récapitulatifs (maquette 5).
--}}
@props(['montant', 'barre' => null, 'ton' => 'brun'])

@php
    $enPromotion = $barre !== null && $barre > $montant;
    $couleur = $ton === 'bronze' ? 'text-bronze-fonce' : 'text-brun';
@endphp

<span {{ $attributes->class('chiffres inline-flex flex-wrap items-baseline gap-x-2 font-semibold') }}>
    <span class="{{ $couleur }}">
        @if ($enPromotion)
            <span class="sr-only">{{ __('vitrine.produit.prix_actuel') }} :</span>
        @endif
        {{ \App\Support\Montant::fcfa($montant) }}
    </span>
    @if ($enPromotion)
        <s class="text-[0.85em] font-normal text-taupe">
            <span class="sr-only">{{ __('vitrine.produit.prix_initial') }} :</span>
            {{ \App\Support\Montant::fcfa($barre) }}
        </s>
    @endif
</span>
