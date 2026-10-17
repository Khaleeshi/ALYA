{{--
    Icônes d'interface AMANI : trait de 1,5 px, extrémités arrondies, 24 px
    par défaut (maquettes). Les logos de marques (Wave, Orange Money,
    WhatsApp, Instagram, TikTok) ne sont pas dessinés ici : ils viendront des
    fichiers officiels.
--}}
@props(['nom', 'taille' => 24])

@php
    $traces = [
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'recherche' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>',
        'compte' => '<circle cx="12" cy="8.5" r="3.75"/><path d="M5 20c.9-3.6 3.7-5.75 7-5.75s6.1 2.15 7 5.75"/>',
        'sac' => '<path d="M5.5 8.5h13l-1 11.5h-11l-1-11.5Z"/><path d="M9 8.5V7a3 3 0 0 1 6 0v1.5"/>',
        'panier' => '<path d="M3 4h2.2l2.1 10.2a1.5 1.5 0 0 0 1.5 1.2h8.1a1.5 1.5 0 0 0 1.5-1.1L20 8H6.2"/><circle cx="9.5" cy="19.5" r="1.2"/><circle cx="17" cy="19.5" r="1.2"/>',
        'coeur' => '<path d="M12 19.5s-7.5-4.4-7.5-9.6A4 4 0 0 1 12 8a4 4 0 0 1 7.5 1.9c0 5.2-7.5 9.6-7.5 9.6Z"/>',
        'fermer' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'chevron' => '<path d="m9 5 7 7-7 7"/>',
        'chevron-bas' => '<path d="m6 9 6 6 6-6"/>',
        'fleche' => '<path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'moins' => '<path d="M5 12h14"/>',
        'coche' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
        'alerte' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5v5.5M12 16.2v.3"/>',
        'info' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5.5M12 7.8v.3"/>',
        'camion' => '<path d="M3 6.5h10.5v9H3zM13.5 9.5h3.8l2.7 3v3h-6.5"/><circle cx="7" cy="17" r="1.8"/><circle cx="16.5" cy="17" r="1.8"/>',
        'cadenas' => '<rect x="5" y="10.5" width="14" height="9.5" rx="2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5M12 14.5v2"/>',
        'bouclier' => '<path d="M12 3.5 5 6v5.5c0 4.3 3 7.6 7 9 4-1.4 7-4.7 7-9V6l-7-2.5Z"/><path d="m9 12 2 2 4-4"/>',
        'diamant' => '<path d="M7 4.5h10l3.5 5L12 20 3.5 9.5 7 4.5Z"/><path d="M3.5 9.5h17M9.5 4.5 8 9.5l4 10.5 4-10.5-1.5-5"/>',
        'cadeau' => '<rect x="4" y="9" width="16" height="11" rx="1.5"/><path d="M3 9h18M12 9v11M12 9c-1.5-3.5-5.5-4.5-5.5-2s3 2 5.5 2Zm0 0c1.5-3.5 5.5-4.5 5.5-2s-3 2-5.5 2Z"/>',
        'casque' => '<path d="M4.5 14v-2a7.5 7.5 0 0 1 15 0v2"/><rect x="3.5" y="13.5" width="4" height="6" rx="1.5"/><rect x="16.5" y="13.5" width="4" height="6" rx="1.5"/><path d="M18.5 19.5c0 1.2-2 2-4.5 2"/>',
        'corbeille' => '<path d="M4.5 7h15M9.5 7V5h5v2M6.5 7l1 12.5h9l1-12.5M10 11v5M14 11v5"/>',
        'position' => '<path d="M12 21s-6.5-5.9-6.5-11a6.5 6.5 0 0 1 13 0c0 5.1-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/>',
        'carte' => '<rect x="3" y="5.5" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/>',
        'horloge' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'partager' => '<circle cx="17.5" cy="6" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><circle cx="17.5" cy="18" r="2.5"/><path d="m8.7 10.8 6.6-3.6M8.7 13.2l6.6 3.6"/>',
        'maison' => '<path d="M4 10.5 12 4l8 6.5V20h-5.5v-5.5h-5V20H4v-9.5Z"/>',
        'recu' => '<path d="M6 3.5h12v17l-2.5-1.5-2 1.5-1.5-1.5-1.5 1.5-2-1.5L6 20.5v-17Z"/><path d="M9 8h6M9 11.5h6M9 15h3.5"/>',
        'etoile' => '<path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.8l-5.2 2.8 1-5.8-4.3-4.1 5.9-.9L12 3.5Z"/>',
        'boite' => '<path d="M12 3 20 7.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="M4 7.5 12 12l8-4.5M12 12v9"/>',
        'retour' => '<path d="M9 6.5 4.5 11 9 15.5"/><path d="M4.5 11H15a4.5 4.5 0 0 1 0 9h-2.5"/>',
        'deconnexion' => '<path d="M14 4.5H6.5A1.5 1.5 0 0 0 5 6v12a1.5 1.5 0 0 0 1.5 1.5H14"/><path d="M10 12h10M16.5 8.5 20 12l-3.5 3.5"/>',
        'oeil' => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
        'cible' => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>',
        'feuille' => '<path d="M5.5 18.5C5.5 10 10.5 5 19.5 4.5c-.5 9-5.5 14-14 14Z"/><path d="M5.5 18.5 13 11"/>',
        'personnes' => '<circle cx="9" cy="8.5" r="3"/><path d="M3.5 19c.6-3 2.8-4.8 5.5-4.8s4.9 1.8 5.5 4.8"/><circle cx="16.5" cy="9.5" r="2.5"/><path d="M15.5 14.4c2.4-.4 4.4 1.1 5 3.6"/>',
        'couronne' => '<path d="M4.5 17.5 3.5 8l5 4 3.5-6 3.5 6 5-4-1 9.5h-15Z"/><path d="M4.5 20.5h15"/>',
        'enveloppe' => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'avion' => '<path d="M20.5 3.5 3.5 10.5l7 2.5 2.5 7 7.5-16.5Z"/><path d="m10.5 13 4-4"/>',
        'cloche' => '<path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 2h-15l1.5-2Z"/><path d="M10 20.5a2 2 0 0 0 4 0"/>',
        'boutique' => '<path d="M4 9.5 5.5 4.5h13L20 9.5"/><path d="M4 9.5c0 1.4 1.2 2.5 2.7 2.5s2.6-1.1 2.6-2.5c0 1.4 1.2 2.5 2.7 2.5s2.7-1.1 2.7-2.5c0 1.4 1.1 2.5 2.6 2.5S20 10.9 20 9.5"/><path d="M5.5 12v7.5h13V12M10 19.5V15h4v4.5"/>',
        'crayon' => '<path d="M15.5 4.5 19.5 8.5 8.5 19.5h-4v-4l11-11Z"/><path d="m13.5 6.5 4 4"/>',
        'lien-externe' => '<path d="M14 4.5h5.5V10M19.5 4.5 11 13"/><path d="M18 14v4.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 4 18.5v-11A1.5 1.5 0 0 1 5.5 6H10"/>',
        'levres' => '<path d="M3 12c2.5-3.5 5-5 7-5 .8 0 1.4.4 2 1 .6-.6 1.2-1 2-1 2 0 4.5 1.5 7 5-2.5 3.5-5.5 5.5-9 5.5S5.5 15.5 3 12Z"/><path d="M3 12c3 1 6 1.3 9 1.3s6-.3 9-1.3"/>',
        'eclat' => '<path d="M12 3.5c.6 4 2.5 6 6.5 6.5-4 .6-5.9 2.5-6.5 6.5-.6-4-2.5-5.9-6.5-6.5 4-.5 5.9-2.5 6.5-6.5Z"/><path d="M18.5 15.5v4M16.5 17.5h4"/>',
        'goutte' => '<path d="M12 3.5s-6 6.6-6 10.8a6 6 0 0 0 12 0C18 10.1 12 3.5 12 3.5Z"/><path d="M9 14.5a3 3 0 0 0 3 3"/>',
        'filtres' => '<path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/>',
    ];

    $trace = $traces[$nom] ?? throw new InvalidArgumentException("Icône inconnue : {$nom}");
@endphp

<svg
    {{ $attributes->class('shrink-0') }}
    width="{{ $taille }}"
    height="{{ $taille }}"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.5"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
>{!! $trace !!}</svg>
