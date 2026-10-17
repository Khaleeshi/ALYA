{{-- « Mon profil » dans l'espace client. --}}
<x-layouts.espace-client :titre="__('comptes.profil.titre')">
    <x-amani.bloc :titre="__('comptes.profil.titre')" icone="compte">
        <form method="POST" action="{{ route_langue('mon-profil.enregistrer') }}" class="flex max-w-xl flex-col gap-5" novalidate>
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <x-amani.champ nom="prenom" :libelle="__('comptes.inscription.prenom')" :valeur="$cliente->prenom" autocomplete="given-name" :requis="true" />
                <x-amani.champ nom="nom" :libelle="__('comptes.inscription.nom')" :valeur="$cliente->nom" autocomplete="family-name" :requis="true" />
            </div>

            <x-amani.champ nom="email" type="email" :libelle="__('comptes.inscription.email')" :valeur="$cliente->email" :aide="__('comptes.inscription.email_aide')" autocomplete="email" />

            <div class="flex flex-col gap-1.5">
                <p class="text-sm font-medium text-brun">{{ __('comptes.mon_compte.telephone') }}</p>
                <p class="chiffres flex h-12 items-center gap-2 rounded-champ border border-filet bg-creme px-4 text-base text-brun">
                    <x-amani.drapeau-senegal />
                    {{ \App\Support\Telephone::afficher($cliente->telephone) }}
                </p>
                <p class="text-sm text-taupe">{{ __('comptes.profil.telephone_aide') }}</p>
            </div>

            <x-amani.case nom="offres" :libelle="__('comptes.inscription.offres')" :coche="$offres" class="border-t border-filet pt-5" />

            <x-amani.bouton class="self-start">{{ __('comptes.profil.enregistrer') }}</x-amani.bouton>
        </form>
    </x-amani.bloc>
</x-layouts.espace-client>
