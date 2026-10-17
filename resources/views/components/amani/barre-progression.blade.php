{{--
    Barre de progression (maquette 4 : palier VIP). valeur et maximum en FCFA ;
    le slot porte la phrase au-dessus (« Plus que 31 000 FCFA pour… »).
--}}
@props(['valeur', 'maximum', 'libelle'])

@php
    $pourcentage = $maximum > 0 ? (int) round(min(1, max(0, $valeur / $maximum)) * 100) : 0;
@endphp

<div {{ $attributes->class('flex flex-col gap-2') }}>
    @if ($slot->isNotEmpty())
        <p class="text-sm text-brun">{{ $slot }}</p>
    @endif
    <div
        class="h-2 overflow-hidden rounded-full bg-nude-pale"
        role="progressbar"
        aria-label="{{ $libelle }}"
        aria-valuemin="0"
        aria-valuemax="{{ $maximum }}"
        aria-valuenow="{{ $valeur }}"
    >
        <div class="fond-dore h-full rounded-full" style="width: {{ $pourcentage }}%"></div>
    </div>
    <p class="chiffres self-end text-xs text-taupe">{{ \App\Support\Montant::fcfa($valeur) }} / {{ \App\Support\Montant::fcfa($maximum) }}</p>
</div>
