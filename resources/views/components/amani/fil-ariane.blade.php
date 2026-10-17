{{--
    Fil d'Ariane (maquette 2) : « Accueil › Gloss › Gloss Amani ».
    liens : liste de ['libelle' => …, 'url' => …] ; le dernier est la page affichée.
--}}
@props(['liens'])

<nav {{ $attributes->class('text-sm text-taupe') }} aria-label="{{ __('vitrine.fil_ariane') }}">
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
        @foreach ($liens as $lien)
            <li class="flex items-center gap-2">
                @if (! $loop->first)
                    <x-amani.icone nom="chevron" :taille="14" class="text-taupe/70" />
                @endif
                @if ($loop->last)
                    <span aria-current="page" class="text-brun">{{ $lien['libelle'] }}</span>
                @else
                    <a href="{{ $lien['url'] }}" class="hover:text-bronze-fonce">{{ $lien['libelle'] }}</a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
