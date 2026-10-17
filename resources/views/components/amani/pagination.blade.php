{{--
    Pagination des listes de produits : précédent, numéros de page (avec
    « … » quand il y en a beaucoup), suivant. Rien si une seule page.
    pages : LengthAwarePaginator (withQueryString pour garder les filtres).
--}}
@props(['pages'])

@if ($pages->hasPages())
    @php
        $courante = $pages->currentPage();
        $derniere = $pages->lastPage();
        $numeros = collect([1, $derniere, $courante - 1, $courante, $courante + 1])
            ->filter(fn (int $n): bool => $n >= 1 && $n <= $derniere)
            ->unique()
            ->sort()
            ->values();
        $pastille = 'inline-flex size-10 items-center justify-center rounded-full text-[15px] transition-colors';
    @endphp

    <nav {{ $attributes->class('flex items-center justify-center gap-1.5') }} aria-label="{{ __('vitrine.boutique.pages') }}">
        @if ($pages->onFirstPage())
            <span class="{{ $pastille }} text-taupe/50" aria-hidden="true"><x-amani.icone nom="chevron" :taille="18" class="rotate-180" /></span>
        @else
            <a href="{{ $pages->previousPageUrl() }}" rel="prev" class="{{ $pastille }} text-brun hover:bg-nude-pale" aria-label="{{ strip_tags(html_entity_decode(__('pagination.previous'))) }}"><x-amani.icone nom="chevron" :taille="18" class="rotate-180" /></a>
        @endif

        @foreach ($numeros as $numero)
            @if (! $loop->first && $numero - $numeros[$loop->index - 1] > 1)
                <span class="px-1 text-taupe" aria-hidden="true">…</span>
            @endif
            @if ($numero === $courante)
                <span class="{{ $pastille }} chiffres bg-cacao font-semibold text-blanc" aria-current="page">{{ $numero }}</span>
            @else
                <a href="{{ $pages->url($numero) }}" class="{{ $pastille }} chiffres text-brun hover:bg-nude-pale">{{ $numero }}</a>
            @endif
        @endforeach

        @if ($pages->hasMorePages())
            <a href="{{ $pages->nextPageUrl() }}" rel="next" class="{{ $pastille }} text-brun hover:bg-nude-pale" aria-label="{{ strip_tags(html_entity_decode(__('pagination.next'))) }}"><x-amani.icone nom="chevron" :taille="18" /></a>
        @else
            <span class="{{ $pastille }} text-taupe/50" aria-hidden="true"><x-amani.icone nom="chevron" :taille="18" /></span>
        @endif
    </nav>
@endif
