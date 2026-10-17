{{--
    Étapes de la commande (maquettes 4 et 5) : Panier, Livraison, Paiement,
    Confirmation. courante : numéro de l'étape en cours (1 à 4).
--}}
@props(['courante' => 1])

@php
    $etapes = ['panier', 'livraison', 'paiement', 'confirmation'];
@endphp

<nav {{ $attributes }} aria-label="{{ __('vitrine.etapes.titre') }}">
    <ol class="flex items-start">
        @foreach ($etapes as $index => $cle)
            @php
                $numero = $index + 1;
                $faite = $numero < $courante;
                $enCours = $numero === $courante;
            @endphp
            <li class="relative flex flex-1 flex-col items-center gap-2 text-center" @if ($enCours) aria-current="step" @endif>
                @if ($index > 0)
                    <span @class(['absolute top-[15px] right-1/2 h-px w-full', 'bg-bronze' => $numero <= $courante, 'bg-filet' => $numero > $courante]) aria-hidden="true"></span>
                @endif
                <span @class([
                    'relative z-10 flex size-8 items-center justify-center rounded-full text-sm font-semibold',
                    'fond-dore text-blanc' => $faite || $enCours,
                    'border border-taupe/50 bg-blanc text-taupe' => ! $faite && ! $enCours,
                ])>
                    @if ($faite)
                        <x-amani.icone nom="coche" :taille="16" />
                    @else
                        {{ $numero }}
                    @endif
                </span>
                <span @class(['text-sm', 'font-semibold text-brun' => $enCours, 'text-taupe' => ! $enCours])>
                    {{ __('vitrine.etapes.'.$cle) }}
                    @if ($faite)<span class="sr-only">({{ __('vitrine.etapes.faite') }})</span>@endif
                    @if ($enCours)<span class="sr-only">({{ __('vitrine.etapes.en_cours') }})</span>@endif
                </span>
            </li>
        @endforeach
    </ol>
</nav>
