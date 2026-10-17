{{--
    Bloc de contenu (maquettes 6 et 8 : « Mon profil », « Dernière commande »,
    « Commandes récentes ») : carte blanche, icône dans un rond nude, titre en
    Playfair, lien à droite (« Modifier », « Voir toutes les commandes → »).
--}}
@props(['titre', 'icone' => null, 'lien' => null, 'libelleLien' => null])

<section {{ $attributes->class('rounded-carte bg-blanc p-5 shadow-carte sm:p-6') }}>
    <header class="mb-5 flex items-center gap-3">
        @if ($icone)
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-nude-pale text-bronze">
                <x-amani.icone :nom="$icone" :taille="20" />
            </span>
        @endif
        <h2 class="titre flex-1 text-xl text-brun">{{ $titre }}</h2>
        @if ($lien)
            <a href="{{ $lien }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brun underline decoration-or underline-offset-4 hover:text-bronze-fonce">
                {{ $libelleLien ?? __('vitrine.voir_tout') }}
            </a>
        @endif
    </header>

    {{ $slot }}
</section>
