{{--
    Bibliothèque de composants ALYA (audit 9.3), pour la validation par la
    direction. Ouverte en développement et en recette, jamais en production.
    Les noms et prix des vignettes sont des exemples tirés de l'audit, en
    attendant les fiches réelles du catalogue (phase 2).
--}}
@php
    $navigationExemple = [
        ['libelle' => 'Produits', 'url' => '#'],
        ['libelle' => 'Éditions limitées', 'url' => '#'],
        ['libelle' => 'Collections', 'url' => '#'],
        ['libelle' => 'Offres', 'url' => '#'],
        ['libelle' => 'VIP', 'url' => '#'],
        ['libelle' => 'À propos', 'url' => '#'],
        ['libelle' => 'Aide', 'url' => '#'],
    ];

    $couleurs = [
        ['Noir profond', '#070707', 'Fond de page'],
        ['Noir surface', '#111111', 'Cartes, tiroirs'],
        ['Noir relief', '#1A1A1A', 'Champs, survols'],
        ['Filet', '#2A2620', 'Bordures 1 px'],
        ['Or champagne', '#C8A95A', 'Accent unique, bouton principal'],
        ['Or clair', '#E9D9AE', 'Grands titres dorés'],
        ['Or sombre', '#7D6432', 'Filets dorés, états inactifs'],
        ['Ivoire', '#F3EDE2', 'Texte principal'],
        ['Gris chaud', '#8E877B', 'Texte secondaire'],
        ['Vert état', '#3E7B5C', 'Livrée, disponible'],
        ['Ambre état', '#B8862B', 'Stock faible, en attente'],
        ['Rouge état', '#A03A32', 'Rupture, annulée, alerte'],
    ];

    $icones = ['menu', 'recherche', 'compte', 'sac', 'coeur', 'fermer', 'chevron', 'plus', 'moins', 'coche', 'alerte'];
@endphp

<x-layouts.vitrine titre="Système de design" :liens="$navigationExemple">
    <div class="conteneur py-16 lg:py-24">
        <header class="max-w-3xl border-b border-filet pb-12">
            <p class="etiquette text-or">Bibliothèque de composants · phase 1</p>
            <h1 class="titre-page mt-6 text-4xl text-ivoire lg:text-6xl">Système de design ALYA</h1>
            <p class="mt-6 text-lg text-gris">
                Les briques de la boutique, à valider avant la construction des pages. Le luxe tient dans la retenue :
                un seul or, des angles vifs, aucune ombre, aucune paillette dessinée. Les paillettes vivront dans les photographies.
            </p>
            <p class="mt-4 text-sm text-gris">Les noms et les prix des exemples viennent de l'audit ; ils seront remplacés par les fiches du catalogue.</p>
        </header>

        {{-- Couleurs --}}
        <section class="border-b border-filet py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Couleurs</h2>
            <p class="mt-3 max-w-2xl text-gris">Le texte n'est jamais blanc pur. Les couleurs d'état ne décorent jamais : sur noir, le vert et le rouge marquent un repère, le texte reste ivoire.</p>
            <ul class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($couleurs as [$nom, $code, $usage])
                    <li>
                        <span class="block aspect-[4/3] border border-filet" style="background-color: {{ $code }}"></span>
                        <p class="mt-3 text-sm text-ivoire">{{ $nom }}</p>
                        <p class="chiffres text-xs text-gris">{{ $code }}</p>
                        <p class="mt-1 text-xs text-gris">{{ $usage }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Typographie --}}
        <section class="border-b border-filet py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Typographie</h2>
            <div class="mt-10 grid gap-12 lg:grid-cols-2">
                <div class="flex flex-col gap-6">
                    <p class="etiquette text-gris">Bodoni Moda · titres, prix, compte à rebours</p>
                    <p class="titre-page text-5xl text-ivoire">Gloss Diamond Glow</p>
                    <p class="titre text-3xl text-ivoire">Complétez votre rituel</p>
                    <p class="signature text-2xl text-or-clair">{{ __('vitrine.signature') }}</p>
                    <p class="chiffres text-4xl text-ivoire">02 : 14 : 37 : 09</p>
                </div>
                <div class="flex flex-col gap-6">
                    <p class="etiquette text-gris">Jost · texte, navigation, boutons, formulaires</p>
                    <p class="etiquette text-ivoire">Ajouter au panier · Étiquette 12 px, capitales espacées de 14 %</p>
                    <p class="max-w-prose text-ivoire">
                        Texte courant en 16 px, interligne 1,6. Un baume-gloss haute brillance, non collant, enrichi en huile de baobab.
                        Texture fondante, tenue six heures, fini miroir.
                    </p>
                    <p class="max-w-prose text-sm text-gris">Texte secondaire en gris chaud, pour les précisions et les aides des formulaires.</p>
                </div>
            </div>
        </section>

        {{-- Boutons --}}
        <section class="border-b border-filet py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Boutons</h2>
            <p class="mt-3 max-w-2xl text-gris">Un seul bouton doré plein par écran, pour l'action principale. Les autres actions en filet ou en lien souligné.</p>
            <div class="mt-10 flex flex-wrap items-center gap-6">
                <x-alya.bouton type="button">Payer avec Wave</x-alya.bouton>
                <x-alya.bouton variante="secondaire" type="button">Ajouter</x-alya.bouton>
                <x-alya.bouton variante="discret" type="button">Continuer mes achats</x-alya.bouton>
                <x-alya.bouton variante="lien" type="button">Voir le détail</x-alya.bouton>
                <x-alya.bouton variante="discret" type="button" disabled>Indisponible</x-alya.bouton>
            </div>
        </section>

        {{-- Champs --}}
        <section class="border-b border-filet py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Champs</h2>
            <div class="mt-10 grid max-w-3xl gap-8 sm:grid-cols-2">
                <x-alya.champ nom="exemple_prenom" libelle="Prénom" :requis="true" placeholder="Awa" />
                <x-alya.champ nom="exemple_repere" libelle="Point de repère" aide="Par exemple : face à la pharmacie, portail vert." />
                <x-alya.champ nom="exemple_telephone" type="tel" libelle="Téléphone" :requis="true" valeur="77 12" erreur="Indiquez un numéro joignable, par exemple 77 123 45 67." />
                <div class="flex items-end pb-3">
                    <x-alya.case nom="exemple_offres" libelle="J'accepte de recevoir les offres et nouveautés ALYA." :coche="true" />
                </div>
            </div>
        </section>

        {{-- Icônes, prix, badges, états --}}
        <section class="grid gap-16 border-b border-filet py-16 lg:grid-cols-2" data-apparition>
            <div>
                <h2 class="titre text-3xl text-ivoire">Icônes</h2>
                <p class="mt-3 text-gris">Fonctionnelles uniquement, trait de 1,25 px.</p>
                <ul class="mt-8 flex flex-wrap gap-6 text-ivoire">
                    @foreach ($icones as $icone)
                        <li class="flex w-16 flex-col items-center gap-2">
                            <x-alya.icone :nom="$icone" />
                            <span class="text-[11px] text-gris">{{ $icone }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="flex flex-col gap-10">
                <div>
                    <h2 class="titre text-3xl text-ivoire">Prix</h2>
                    <div class="mt-6 flex flex-col gap-3 text-2xl">
                        <x-alya.prix :montant="4500" />
                        <x-alya.prix :montant="4050" :barre="4500" />
                    </div>
                </div>
                <div>
                    <h2 class="titre text-3xl text-ivoire">Badges et états</h2>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <x-alya.badge>Nouveau</x-alya.badge>
                        <x-alya.badge>Édition limitée</x-alya.badge>
                        <x-alya.badge>-10 %</x-alya.badge>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <x-alya.pastille-etat etat="succes">Livrée</x-alya.pastille-etat>
                        <x-alya.pastille-etat etat="attention">Stock faible</x-alya.pastille-etat>
                        <x-alya.pastille-etat etat="alerte">Rupture</x-alya.pastille-etat>
                        <x-alya.pastille-etat>Reçue</x-alya.pastille-etat>
                    </div>
                </div>
            </div>
        </section>

        {{-- Vignettes --}}
        <section class="border-b border-filet py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Vignettes produit</h2>
            <p class="mt-3 max-w-2xl text-gris">Photo 4:5 sur noir, un badge au plus. Sur ordinateur, « Ajouter » apparaît au survol ; sur téléphone, il reste visible.</p>
            <div class="mt-10 grid grid-cols-2 gap-x-6 gap-y-12 sm:grid-cols-3 lg:grid-cols-4">
                <x-alya.vignette-produit nom="Gloss Diamond Glow" descriptif="Haute brillance, huile de baobab" :prix="4500" url="#" badge="Nouveau"
                    :teintes="[['nom' => 'Diamond Rose', 'couleur' => '#C98C8C'], ['nom' => 'Caramel Gold', 'couleur' => '#B07A45']]" />
                <x-alya.vignette-produit nom="Gloss Diamond Shine" descriptif="Fini miroir, non collant" :prix="4050" :prix-barre="4500" url="#" badge="-10 %" />
                <x-alya.vignette-produit nom="Gloss Caramel Gold" descriptif="Reflets dorés" :prix="4500" url="#" />
                <x-alya.vignette-produit nom="Gloss Diamond Rose" descriptif="Rose lumineux" :prix="4500" url="#" />
            </div>
        </section>

        {{-- Panier --}}
        <section class="border-b border-filet py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Tiroir du panier</h2>
            <p class="mt-3 max-w-2xl text-gris">S'ouvre depuis la droite en 320 ms après un ajout ou par le sac de l'en-tête ; se ferme par la croix, Échap ou un clic à côté.</p>
            <div class="mt-8">
                <x-alya.bouton variante="secondaire" type="button" x-data x-on:click="$dispatch('ouvrir-panier-exemple')">Ouvrir le tiroir d'exemple</x-alya.bouton>
            </div>

            <x-alya.tiroir-panier evenement="ouvrir-panier-exemple">
                <x-alya.ligne-panier nom="Gloss Diamond Glow" teinte="Diamond Rose" :quantite="2" :prix="4500" />
                <x-alya.ligne-panier nom="Gloss Diamond Shine" :quantite="1" :prix="4050" :prix-barre="4500" />
                <x-slot:pied>
                    <div class="mb-6 flex items-baseline justify-between">
                        <span class="etiquette text-gris">{{ __('vitrine.panier.sous_total') }}</span>
                        <x-alya.prix :montant="13050" class="text-xl" />
                    </div>
                    <x-alya.bouton :pleine-largeur="true" href="#">{{ __('vitrine.panier.voir') }}</x-alya.bouton>
                </x-slot:pied>
            </x-alya.tiroir-panier>
        </section>

        {{-- Tableau --}}
        <section class="py-16" data-apparition>
            <h2 class="titre text-3xl text-ivoire">Tableau d'administration</h2>
            <p class="mt-3 max-w-2xl text-gris">Densité utile, chiffres tabulaires, états en pastilles de texte. L'administration Filament reprend la même palette.</p>
            <x-alya.tableau class="mt-8" legende="Exemple de commandes" :colonnes="['Commande', 'Cliente', 'Zone', 'Statut', ['libelle' => 'Total', 'nombre' => true]]">
                <tr>
                    <td class="chiffres">001</td><td>Awa D.</td><td>Plateau</td>
                    <td><x-alya.pastille-etat>Reçue</x-alya.pastille-etat></td><td class="nombre">{{ \App\Support\Montant::fcfa(9000) }}</td>
                </tr>
                <tr>
                    <td class="chiffres">002</td><td>Fatou S.</td><td>Almadies</td>
                    <td><x-alya.pastille-etat etat="attention">En attente de livraison</x-alya.pastille-etat></td><td class="nombre">{{ \App\Support\Montant::fcfa(13050) }}</td>
                </tr>
                <tr>
                    <td class="chiffres">003</td><td>Mariama B.</td><td>Parcelles</td>
                    <td><x-alya.pastille-etat etat="succes">Livrée</x-alya.pastille-etat></td><td class="nombre">{{ \App\Support\Montant::fcfa(4500) }}</td>
                </tr>
            </x-alya.tableau>
        </section>
    </div>
</x-layouts.vitrine>
