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
