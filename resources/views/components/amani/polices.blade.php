{{--
    Préchargement des polices visibles dès le premier écran (Jost et Playfair
    Display), pour éviter la bascule de police au chargement.
--}}
<link rel="preload" href="{{ Vite::asset('resources/fonts/jost-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ Vite::asset('resources/fonts/playfair-display-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
