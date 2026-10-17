{{--
    Carte de catégorie ou de collection (maquettes 1 et 3) : grande carte aux
    coins arrondis, titre en Playfair, une phrase, bouton « Découvrir → ».
    photo : média Spatie ; sans photo, un dégradé nude en attendant.
--}}
@props(['titre', 'texte' => null, 'url', 'photo' => null, 'action' => null])

<article {{ $attributes->class('group relative isolate flex min-h-44 overflow-hidden rounded-carte bg-gradient-to-br from-nude-pale via-nude-pale to-nude shadow-carte') }}>
    @if ($photo)
        <x-amani.image :media="$photo" alt="" sizes="(min-width: 1024px) 33vw, 100vw" class="absolute inset-0 -z-10 size-full object-cover transition-transform duration-500 ease-amani group-hover:scale-[1.03]" />
    @endif

    <div class="flex max-w-[60%] flex-col justify-center gap-2 p-6">
        <h3 class="titre text-2xl tracking-[0.06em] text-brun uppercase">{{ $titre }}</h3>
        @if ($texte)
            <p class="text-sm leading-snug text-brun/85">{{ $texte }}</p>
        @endif
        <x-amani.bouton :href="$url" taille="petite" icone-apres="fleche" class="mt-2 self-start">{{ $action ?? __('vitrine.produit.decouvrir') }}</x-amani.bouton>
    </div>
</article>
