{{--
    Carte raccourci (maquette 8 : « Mes commandes · 5 commandes · Voir
    l'historique → »). N'afficher qu'avec une vraie valeur et une vraie page.
--}}
@props(['icone', 'titre', 'valeur' => null, 'lien', 'libelleLien'])

<a href="{{ $lien }}" {{ $attributes->class('group flex gap-3 rounded-carte bg-blanc p-4 shadow-carte transition-shadow hover:shadow-survol') }}>
    <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-nude-pale text-bronze">
        <x-amani.icone :nom="$icone" :taille="22" />
    </span>
    <span class="flex min-w-0 flex-col gap-0.5">
        <span class="text-sm font-semibold text-brun">{{ $titre }}</span>
        @if ($valeur !== null)
            <span class="text-sm font-medium text-bronze-fonce">{{ $valeur }}</span>
        @endif
        <span class="mt-1 inline-flex items-center gap-1 text-[13px] text-brun group-hover:text-bronze-fonce">
            {{ $libelleLien }}
            <x-amani.icone nom="fleche" :taille="14" />
        </span>
    </span>
</a>
