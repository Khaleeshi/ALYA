{{--
    Menu latéral de l'espace client (maquette 8). liens : liste de
    ['libelle' => …, 'url' => …, 'icone' => …, 'actif' => bool, 'compteur' => ?int].
--}}
@props(['liens' => [], 'libelle'])

<nav {{ $attributes }} aria-label="{{ $libelle }}">
    <ul class="flex flex-col gap-1">
        @foreach ($liens as $lien)
            @php($actif = $lien['actif'] ?? false)
            <li>
                <a
                    href="{{ $lien['url'] }}"
                    @class([
                        'flex items-center gap-3 rounded-champ px-4 py-3 text-[15px] transition-colors',
                        'bg-nude-pale font-semibold text-brun' => $actif,
                        'text-brun hover:bg-nude-pale/60' => ! $actif,
                    ])
                    @if ($actif) aria-current="page" @endif
                >
                    <x-amani.icone :nom="$lien['icone']" :taille="22" :class="$actif ? 'text-bronze' : 'text-taupe'" />
                    <span class="flex-1">{{ $lien['libelle'] }}</span>
                    @if (($lien['compteur'] ?? 0) > 0)
                        <span class="chiffres rounded-full bg-cacao px-2 py-0.5 text-xs font-semibold text-blanc">{{ $lien['compteur'] }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</nav>
