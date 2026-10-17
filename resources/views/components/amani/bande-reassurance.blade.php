{{--
    Bande de réassurance (maquettes) : livraison, paiement, qualité, cadeaux
    VIP, service client. Textes dans lang/*/vitrine.php (reassurance), à faire
    valider par AMANI.
--}}
@php
    $elements = [
        'livraison' => 'camion',
        'paiement' => 'cadenas',
        'qualite' => 'diamant',
        'vip' => 'cadeau',
        'service' => 'casque',
    ];
@endphp

<section {{ $attributes->class('border-y border-filet bg-creme') }} aria-label="{{ __('vitrine.marque') }}">
    <ul class="conteneur grid grid-cols-2 gap-x-6 gap-y-5 py-6 md:grid-cols-3 lg:grid-cols-5 lg:divide-x lg:divide-filet">
        @foreach ($elements as $cle => $icone)
            <li class="flex items-center gap-3 lg:justify-center lg:px-4 lg:first:pl-0 lg:first:justify-start">
                <x-amani.icone :nom="$icone" :taille="30" class="text-bronze" />
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-brun">{{ __('vitrine.reassurance.'.$cle.'.titre') }}</span>
                    <span class="block text-[13px] text-taupe">{{ __('vitrine.reassurance.'.$cle.'.texte') }}</span>
                </span>
            </li>
        @endforeach
    </ul>
</section>
