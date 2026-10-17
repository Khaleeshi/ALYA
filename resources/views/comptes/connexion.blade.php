{{-- Connexion, étape 1 : le numéro de téléphone (cahier §6.2). --}}
<x-layouts.compte :titre="__('comptes.connexion.titre')" :intro="__('comptes.connexion.intro')">
    <form method="POST" action="{{ route_langue('connexion.envoyer') }}" class="flex flex-col gap-6" novalidate>
        @csrf

        <x-amani.champ-telephone :libelle="__('comptes.connexion.telephone')" autofocus />

        <x-amani.bouton :pleine-largeur="true" icone-apres="fleche">{{ __('comptes.connexion.envoyer') }}</x-amani.bouton>
    </form>

    <p class="mt-8 border-t border-filet pt-6 text-sm text-taupe">{{ __('comptes.connexion.pourquoi') }}</p>
</x-layouts.compte>
