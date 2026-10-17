{{--
    Préchargement des deux polices visibles dès le premier écran (Jost 400 et
    Bodoni Moda 400), pour éviter la bascule de police au chargement.
--}}
<link rel="preload" href="{{ Vite::asset('resources/fonts/jost-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ Vite::asset('resources/fonts/bodoni-moda-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
