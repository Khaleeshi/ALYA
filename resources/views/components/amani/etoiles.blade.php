{{--
    Note des avis vérifiés (cahier §5.3) : cinq étoiles, demi-étoile comprise,
    et le nombre d'avis. N'afficher qu'avec de vrais avis.
--}}
@props(['note', 'avis' => null])

@php
    $note = max(0, min(5, (float) $note));
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1.5 text-sm text-taupe') }}>
    <span class="flex" aria-hidden="true">
        @foreach (range(1, 5) as $position)
            @php($remplissage = max(0, min(1, $note - ($position - 1))))
            <svg width="16" height="16" viewBox="0 0 24 24" class="shrink-0">
                <defs>
                    <linearGradient id="etoile-{{ $position }}-{{ (int) ($remplissage * 100) }}">
                        <stop offset="{{ $remplissage * 100 }}%" stop-color="#e3a33b"/>
                        <stop offset="{{ $remplissage * 100 }}%" stop-color="#e8d5be"/>
                    </linearGradient>
                </defs>
                <path d="m12 2.8 2.8 5.8 6.3.8-4.6 4.4 1.2 6.3L12 17l-5.7 3.1 1.2-6.3-4.6-4.4 6.3-.8L12 2.8Z" fill="url(#etoile-{{ $position }}-{{ (int) ($remplissage * 100) }})"/>
            </svg>
        @endforeach
    </span>
    <span class="sr-only">{{ __('vitrine.produit.note', ['note' => number_format($note, 1, ',', '')]) }}</span>
    <span class="chiffres" aria-hidden="true">{{ number_format($note, 1, ',', '') }}</span>
    @if ($avis !== null)
        <span class="chiffres">({{ trans_choice('vitrine.produit.avis', $avis, ['nombre' => $avis]) }})</span>
    @endif
</span>
