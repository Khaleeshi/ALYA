{{--
    Marque AMANI COSMETICS en lettres, en attendant le logo vectoriel officiel
    (emblème lotus ou couronne à confirmer, cahier §2.1).
--}}
@props(['taille' => 'normale'])

@php
    [$amani, $cosmetics] = match ($taille) {
        'petite' => ['text-[22px]', 'text-[7px]'],
        'grande' => ['text-[44px]', 'text-[11px]'],
        default => ['text-[30px]', 'text-[8.5px]'],
    };
@endphp

<span {{ $attributes->class('inline-flex flex-col items-center leading-none text-brun') }}>
    <span class="titre {{ $amani }} tracking-[0.06em]">AMANI</span>
    <span class="{{ $cosmetics }} mt-1 font-medium tracking-[0.55em] uppercase">Cosmetics</span>
</span>
