{{--
    Un badge au plus par vignette : « Nouveau », « Édition limitée » ou « -10 % ».
    Jamais « Best-seller », « Populaire » ni « Recommandé » (audit 9.2).
--}}
<span {{ $attributes->class('etiquette inline-flex items-center border border-or-sombre bg-noir px-2.5 py-1 text-or-clair') }}>{{ $slot }}</span>
