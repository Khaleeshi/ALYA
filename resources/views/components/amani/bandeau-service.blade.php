{{--
    Bandeau de service en haut de page (cahier §4.1, maquettes) : livraison,
    qualité, paiement sécurisé ; à droite FAQ, Contact, Suivi de commande et
    la devise (FCFA). liens : liens d'aide existants (Navigation::aide()).
--}}
@props(['liens' => []])

<div {{ $attributes->class('bg-cacao text-[12.5px] text-blanc') }}>
    <div class="conteneur flex h-9 items-center justify-between gap-6">
        <ul class="flex min-w-0 items-center gap-6 lg:gap-9">
            <li class="flex items-center gap-2 truncate"><x-amani.icone nom="camion" :taille="16" />{{ __('vitrine.bandeau.livraison') }}</li>
            <li class="hidden items-center gap-2 md:flex"><x-amani.icone nom="cadeau" :taille="16" />{{ __('vitrine.bandeau.qualite') }}</li>
            <li class="hidden items-center gap-2 lg:flex"><x-amani.icone nom="bouclier" :taille="16" />{{ __('vitrine.bandeau.paiement') }}</li>
        </ul>

        <div class="flex shrink-0 items-center gap-4">
            @if (count($liens) > 0)
                <ul class="hidden items-center divide-x divide-blanc/40 sm:flex">
                    @foreach ($liens as $lien)
                        <li class="px-3 first:pl-0"><a href="{{ $lien['url'] }}" class="hover:underline">{{ $lien['libelle'] }}</a></li>
                    @endforeach
                </ul>
            @endif
            <span class="flex items-center gap-2 border-blanc/40 sm:border-l sm:pl-4">
                <x-amani.drapeau-senegal />
                <span>FCFA</span>
                <span class="sr-only">{{ __('vitrine.bandeau.devise') }}</span>
            </span>
        </div>
    </div>
</div>
