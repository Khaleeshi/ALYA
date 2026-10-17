{{-- Case à cocher : coins de 4 px, cochée en bronze. --}}
@props(['nom', 'libelle', 'coche' => false, 'valeur' => '1'])

@php
    $id = $attributes->get('id', 'case-'.$nom);
    $estCochee = (bool) old($nom, $coche);
@endphp

<label for="{{ $id }}" {{ $attributes->only('class')->class('group flex cursor-pointer items-start gap-3 text-sm text-brun') }}>
    <span class="relative mt-0.5 inline-flex size-5 shrink-0">
        <input
            id="{{ $id }}"
            name="{{ $nom }}"
            type="checkbox"
            value="{{ $valeur }}"
            @checked($estCochee)
            {{ $attributes->except(['class', 'id'])->class('peer size-full cursor-pointer appearance-none rounded-badge border border-bronze bg-blanc transition-colors checked:border-bronze checked:bg-bronze group-hover:border-bronze-fonce') }}
        >
        <x-amani.icone nom="coche" :taille="14" class="pointer-events-none absolute inset-0 m-auto text-blanc opacity-0 peer-checked:opacity-100" />
    </span>
    <span>{{ $libelle }}</span>
</label>
