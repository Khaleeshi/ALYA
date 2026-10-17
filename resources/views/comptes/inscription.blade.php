{{-- Création du compte, une fois le numéro vérifié. --}}
<x-layouts.compte :titre="__('comptes.inscription.titre')" :intro="__('comptes.inscription.intro', ['telephone' => \App\Support\Telephone::afficher($telephone)])">
    <form method="POST" action="{{ route_langue('inscription.enregistrer') }}" class="flex flex-col gap-5" novalidate>
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <x-amani.champ nom="prenom" :libelle="__('comptes.inscription.prenom')" autocomplete="given-name" :requis="true" autofocus />
            <x-amani.champ nom="nom" :libelle="__('comptes.inscription.nom')" autocomplete="family-name" :requis="true" />
        </div>

        <x-amani.champ
            nom="email"
            type="email"
            :libelle="__('comptes.inscription.email')"
            :aide="__('comptes.inscription.email_aide')"
            autocomplete="email"
        />

        <div class="flex flex-col gap-4 border-t border-filet pt-5">
            <p class="text-sm text-taupe">{{ __('comptes.inscription.messages_commande') }}</p>
            <x-amani.case nom="offres" :libelle="__('comptes.inscription.offres')" />
        </div>

        <x-amani.bouton :pleine-largeur="true">{{ __('comptes.inscription.envoyer') }}</x-amani.bouton>
    </form>
</x-layouts.compte>
