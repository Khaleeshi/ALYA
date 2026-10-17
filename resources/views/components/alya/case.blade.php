{{--
    Case à cocher carrée, filet doré sombre, coche noire sur or.
--}}
@props(['nom', 'libelle', 'coche' => false, 'valeur' => '1'])

@php
    $id = $attributes->get('id', 'case-'.$nom);
    $estCochee = (bool) old($nom, $coche);
@endphp

<label for="{{ $id }}" {{ $attributes->only('class')->class('group flex cursor-pointer items-start gap-3 text-sm text-ivoire') }}>
    <span class="relative mt-0.5 inline-flex size-[18px] shrink-0">
        <input
            id="{{ $id }}"
            name="{{ $nom }}"
            type="checkbox"
            value="{{ $valeur }}"
            @checked($estCochee)
            {{ $attributes->except(['class', 'id'])->class('peer size-full cursor-pointer appearance-none border border-or-sombre bg-relief transition-colors checked:border-or checked:bg-or group-hover:border-or') }}
        >
        <x-alya.icone nom="coche" :taille="14" class="pointer-events-none absolute inset-0 m-auto text-noir opacity-0 peer-checked:opacity-100" />
    </span>
    <span>{{ $libelle }}</span>
</label>
