{{--
    Titre de section (maquettes) : titre en Playfair, filet nude avec un petit
    cœur doré au milieu, lien « Voir tout → » à droite si une page existe.
--}}
@props(['titre', 'lien' => null, 'niveau' => 'h2'])

<div {{ $attributes->class('flex items-center gap-5') }}>
    <{{ $niveau }} class="titre shrink-0 text-[28px] text-brun lg:text-[34px]">{{ $titre }}</{{ $niveau }}>

    <span class="hidden flex-1 items-center gap-4 sm:flex" aria-hidden="true">
        <span class="h-px flex-1 bg-filet"></span>
        <svg width="16" height="14" viewBox="0 0 24 21" class="shrink-0 text-or"><path d="M12 20.5S0 13.6 0 6.2A6 6 0 0 1 12 3.4a6 6 0 0 1 12 2.8c0 7.4-12 14.3-12 14.3Z" fill="currentColor"/></svg>
        <span class="h-px flex-1 bg-filet"></span>
    </span>

    @if ($lien)
        <a href="{{ $lien }}" class="ml-auto inline-flex shrink-0 items-center gap-2 text-[15px] font-medium text-brun hover:text-bronze-fonce sm:ml-0">
            {{ __('vitrine.voir_tout') }}
            <x-amani.icone nom="fleche" :taille="18" />
        </a>
    @endif
</div>
