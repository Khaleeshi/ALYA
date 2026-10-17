{{-- Bas du menu latéral de l'administration (maquette 6) : lien vers la boutique et signature. --}}
<div class="amani-pied-menu">
    <a href="{{ route_langue('accueil') }}" target="_blank" rel="noopener" class="amani-pied-menu-lien">
        Voir ma boutique
        <x-amani.icone nom="lien-externe" :taille="16" />
    </a>
    <p class="amani-pied-menu-signature">Parce que vous méritez tous de briller.</p>
</div>
