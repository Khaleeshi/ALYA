{{--
    Bannière en haut d'une page (maquettes 4, 7 : « Mon panier », « À propos »,
    « Contactez-nous ») : titre en Playfair, phrase d'accroche, petit cœur doré
    sur un filet. photo : média Spatie affiché à droite ; sans photo, le
    dégradé nude suffit.
--}}
@props(['titre', 'sousTitre' => null, 'photo' => null])

<section {{ $attributes->class('relative isolate overflow-hidden bg-gradient-to-r from-creme via-nude-pale to-nude') }}>
    @if ($photo)
        <x-amani.image :media="$photo" alt="" sizes="(min-width: 1024px) 50vw, 100vw" class="absolute inset-y-0 right-0 -z-10 hidden h-full w-1/2 object-cover md:block" />
    @endif

    <div class="conteneur py-10 lg:py-14">
        <div class="max-w-xl">
            <h1 class="titre text-[38px] text-brun lg:text-[52px]">{{ $titre }}</h1>
            @if ($sousTitre)
                <p class="mt-3 text-lg text-taupe">{{ $sousTitre }}</p>
            @endif
            <span class="mt-5 flex items-center gap-3" aria-hidden="true">
                <svg width="16" height="14" viewBox="0 0 24 21" class="shrink-0 text-or"><path d="M12 20.5S0 13.6 0 6.2A6 6 0 0 1 12 3.4a6 6 0 0 1 12 2.8c0 7.4-12 14.3-12 14.3Z" fill="currentColor"/></svg>
                <span class="h-px w-40 bg-gradient-to-r from-or/70 to-transparent"></span>
            </span>
        </div>
    </div>
</section>
