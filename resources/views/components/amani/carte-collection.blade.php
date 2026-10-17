{{--
    Carte de collection ou de coffret (maquette 3) : photo en haut, nom en
    Playfair, une phrase, bouton « Voir la collection → ».
    photo : média Spatie ; sans photo, un dégradé nude en attendant.
--}}
@props(['titre', 'texte' => null, 'url', 'photo' => null, 'action' => null])

<article {{ $attributes->class('group flex flex-col overflow-hidden rounded-carte bg-blanc shadow-carte transition-shadow duration-300 ease-amani hover:shadow-survol') }}>
    <a href="{{ $url }}" class="block aspect-[4/3] overflow-hidden bg-gradient-to-br from-nude-pale to-nude" tabindex="-1" aria-hidden="true">
        @if ($photo)
            <x-amani.image :media="$photo" alt="" sizes="(min-width: 768px) 33vw, 100vw" class="size-full object-cover transition-transform duration-500 ease-amani group-hover:scale-[1.03]" />
        @endif
    </a>

    <div class="flex flex-1 flex-col items-center gap-1.5 px-5 pt-5 pb-6 text-center">
        <h3 class="titre text-[22px] text-brun">{{ $titre }}</h3>
        @if ($texte)
            <p class="text-sm leading-snug text-taupe">{{ $texte }}</p>
        @endif
        <div class="mt-auto pt-3">
            <x-amani.bouton :href="$url" taille="petite" icone-apres="fleche" aria-label="{{ ($action ?? __('vitrine.collections.voir')).' : '.$titre }}">{{ $action ?? __('vitrine.collections.voir') }}</x-amani.bouton>
        </div>
    </div>
</article>
