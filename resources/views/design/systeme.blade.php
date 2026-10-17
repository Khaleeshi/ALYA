{{--
    Bibliothèque de composants AMANI, pour la validation par la direction.
    Ouverte en développement et en recette, jamais en production.
    Les noms, prix et notes des exemples viennent des maquettes
    (docs/maquettes-amani), en attendant le vrai catalogue.
--}}
@php
    $navigationExemple = collect(config('boutique.navigation'))
        ->keys()
        ->map(fn (string $cle, int $index): array => ['libelle' => __('vitrine.navigation.'.$cle), 'url' => '#', 'actif' => $index === 0])
        ->all();

    $couleurs = [
        ['Page', '#FCF8F3', 'Fond de page'],
        ['Crème', '#F8F3EC', 'Fond clair du cahier, bandes'],
        ['Blanc cassé', '#FFFDFA', 'Cartes, champs, en-tête'],
        ['Nude', '#E8D5BE', 'Fond principal du cahier, bannières'],
        ['Nude pâle', '#F5E8D7', 'Sélection, bouton secondaire'],
        ['Filet', '#EADCCB', 'Bordures'],
        ['Or', '#C6A15B', 'Accent du cahier : ornements, étoiles'],
        ['Bronze', '#9C6B3F', 'Texte doré, icônes, signature'],
        ['Bronze foncé', '#85552C', 'Dégradé des boutons, prix'],
        ['Cacao', '#7E5537', 'Bandeau de service, badges'],
        ['Brun', '#493729', 'Texte principal du cahier'],
        ['Taupe', '#6F5B4C', 'Texte secondaire'],
    ];

    $icones = ['menu', 'recherche', 'compte', 'coeur', 'sac', 'panier', 'fermer', 'chevron', 'fleche', 'plus', 'moins', 'coche', 'info', 'alerte', 'camion', 'cadenas', 'bouclier', 'diamant', 'cadeau', 'casque', 'corbeille', 'position', 'carte', 'horloge', 'partager'];
@endphp

<x-layouts.vitrine titre="Système de design" :liens="$navigationExemple">
    <div class="conteneur flex flex-col gap-20 py-14 lg:py-20">
        <header class="max-w-3xl">
            <p class="sur-titre text-taupe">Bibliothèque de composants</p>
            <h1 class="titre mt-4 text-[44px] text-brun lg:text-6xl">Système de design <span class="manuscrit text-bronze">Amani</span></h1>
            <p class="mt-5 text-lg text-taupe">
                Les briques de la boutique, tirées des maquettes choisies : crème et nude, texte brun, accents or et bronze,
                coins légèrement arrondis, boutons en pilule. À valider avant la construction des pages.
            </p>
            <p class="mt-3 text-sm text-taupe">Les noms, prix et notes des exemples viennent des maquettes ; ils seront remplacés par le vrai catalogue.</p>
        </header>

        {{-- Couleurs --}}
        <section data-apparition>
            <x-amani.titre-section titre="Couleurs" />
            <p class="mt-4 max-w-3xl text-taupe">L'or du cahier (#C6A15B) est trop clair pour être lu sur crème : il sert aux ornements. Les textes dorés, icônes et boutons passent au bronze, lisible.</p>
            <ul class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($couleurs as [$nom, $code, $usage])
                    <li class="overflow-hidden rounded-carte bg-blanc shadow-carte">
                        <span class="block aspect-[4/3] border-b border-filet" style="background-color: {{ $code }}"></span>
                        <div class="p-3">
                            <p class="text-sm font-semibold text-brun">{{ $nom }}</p>
                            <p class="chiffres text-xs text-taupe">{{ $code }}</p>
                            <p class="mt-1 text-xs text-taupe">{{ $usage }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Typographie --}}
        <section data-apparition>
            <x-amani.titre-section titre="Typographie" />
            <div class="mt-8 grid gap-10 rounded-bloc bg-blanc p-8 shadow-carte lg:grid-cols-2">
                <div class="flex flex-col gap-5">
                    <p class="sur-titre text-taupe">Playfair Display · titres</p>
                    <p class="titre text-5xl text-brun">Nos meilleures ventes</p>
                    <p class="titre text-3xl text-brun">Gloss Amani - Nude Glow</p>
                    <p class="titre text-4xl text-brun">Parce que vous méritez tous <span class="manuscrit block text-6xl text-bronze">de briller.</span></p>
                    <p class="sur-titre text-taupe">Great Vibes · signature manuscrite</p>
                </div>
                <div class="flex flex-col gap-5">
                    <p class="sur-titre text-taupe">Jost · texte, menu, boutons, formulaires</p>
                    <p class="text-lg text-brun">Un gloss nude lumineux qui apporte une brillance intense tout en nourrissant vos lèvres. Sa texture légère et non collante révèle une bouche douce et hydratée.</p>
                    <p class="text-sm text-taupe">Texte secondaire en taupe, pour les précisions et les aides des formulaires.</p>
                    <p class="chiffres text-3xl font-semibold text-brun">5 000 FCFA</p>
                    <p class="sur-titre text-taupe">Amani Cosmetics</p>
                </div>
            </div>
        </section>

        {{-- Boutons --}}
        <section data-apparition>
            <x-amani.titre-section titre="Boutons" />
            <div class="mt-8 flex flex-wrap items-center gap-5">
                <x-amani.bouton type="button" icone="panier">Ajouter au panier</x-amani.bouton>
                <x-amani.bouton type="button" variante="secondaire">Acheter maintenant</x-amani.bouton>
                <x-amani.bouton type="button" icone-apres="fleche">Découvrir la collection</x-amani.bouton>
                <x-amani.bouton type="button" icone="cadenas">Confirmer la commande</x-amani.bouton>
                <x-amani.bouton type="button" variante="contour">Continuer mes achats</x-amani.bouton>
                <x-amani.bouton type="button" taille="petite" icone-apres="fleche">Découvrir</x-amani.bouton>
                <x-amani.bouton type="button" variante="lien">Voir le détail</x-amani.bouton>
                <x-amani.bouton type="button" disabled>Indisponible</x-amani.bouton>
            </div>
        </section>

        {{-- Formulaires --}}
        <section data-apparition>
            <x-amani.titre-section titre="Formulaires" />
            <div class="mt-8 grid gap-8 lg:grid-cols-2">
                <div class="flex flex-col gap-5 rounded-bloc bg-blanc p-6 shadow-carte sm:p-8">
                    <h3 class="titre flex items-center gap-3 text-2xl text-brun"><x-amani.icone nom="position" class="text-bronze" /> Informations de livraison</h3>
                    <x-amani.champ nom="exemple_nom" libelle="Nom complet" :requis="true" placeholder="Votre nom complet" />
                    <x-amani.champ-telephone nom="exemple_telephone" libelle="Numéro de téléphone" />
                    <x-amani.zone-texte nom="exemple_adresse" libelle="Adresse de livraison" :requis="true" placeholder="Rue, quartier, point de repère…" />
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-amani.liste nom="exemple_region" libelle="Région" :requis="true" invite="Sélectionner une région" :options="['dakar' => 'Dakar']" />
                        <x-amani.liste nom="exemple_zone" libelle="Zone" :requis="true" invite="Sélectionner une zone" />
                    </div>
                    <x-amani.champ nom="exemple_erreur" libelle="Numéro de téléphone (erreur)" valeur="77 12" erreur="Indiquez un numéro sénégalais, par exemple 77 123 45 67." />
                </div>
                <div class="flex flex-col gap-5 rounded-bloc bg-blanc p-6 shadow-carte sm:p-8">
                    <h3 class="titre flex items-center gap-3 text-2xl text-brun"><x-amani.icone nom="camion" class="text-bronze" /> Mode de livraison</h3>
                    <p class="text-sm font-medium text-brun">Créneaux disponibles</p>
                    <div class="grid grid-cols-2 gap-3">
                        <x-amani.choix nom="exemple_creneau" valeur="09-12" libelle="09h – 12h" :coche="true" />
                        <x-amani.choix nom="exemple_creneau" valeur="12-15" libelle="12h – 15h" />
                        <x-amani.choix nom="exemple_creneau" valeur="15-18" libelle="15h – 18h" />
                        <x-amani.choix nom="exemple_creneau" valeur="18-21" libelle="18h – 21h" :desactive="true" />
                    </div>
                    <x-amani.notice>Les créneaux peuvent varier selon la zone, la disponibilité des livreurs et le nombre de commandes.</x-amani.notice>

                    <h3 class="titre mt-4 flex items-center gap-3 text-2xl text-brun"><x-amani.icone nom="carte" class="text-bronze" /> Mode de paiement</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <x-amani.choix nom="exemple_paiement" valeur="wave" :coche="true" class="justify-center py-6"><span class="font-semibold">Wave</span></x-amani.choix>
                        <x-amani.choix nom="exemple_paiement" valeur="orange-money" class="justify-center py-6"><span class="font-semibold">Orange Money</span></x-amani.choix>
                    </div>
                    <p class="text-xs text-taupe">Les logos officiels de Wave et d'Orange Money remplaceront ces noms dès réception des fichiers.</p>
                    <x-amani.case nom="exemple_cgv" libelle="J'accepte les conditions générales et la politique de retour." :coche="true" />
                </div>
            </div>
        </section>

        {{-- Icônes, prix, badges, états --}}
        <section class="grid gap-12 lg:grid-cols-2" data-apparition>
            <div>
                <x-amani.titre-section titre="Icônes" />
                <ul class="mt-8 grid grid-cols-5 gap-4 text-brun sm:grid-cols-7">
                    @foreach ($icones as $icone)
                        <li class="flex flex-col items-center gap-1.5 rounded-champ bg-blanc py-3 shadow-carte">
                            <x-amani.icone :nom="$icone" class="text-bronze" />
                            <span class="text-[11px] text-taupe">{{ $icone }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="flex flex-col gap-8">
                <div>
                    <x-amani.titre-section titre="Prix et avis" />
                    <div class="mt-6 flex flex-col gap-3">
                        <x-amani.prix :montant="5000" class="text-2xl" />
                        <x-amani.prix :montant="4500" :barre="5000" class="text-2xl" />
                        <x-amani.prix :montant="16500" ton="bronze" class="text-xl" />
                        <x-amani.etoiles :note="4.8" :avis="124" />
                    </div>
                </div>
                <div>
                    <x-amani.titre-section titre="Badges et états" />
                    <div class="mt-6 flex flex-wrap gap-3">
                        <x-amani.badge>Best-seller</x-amani.badge>
                        <x-amani.badge ton="rouge">Promo</x-amani.badge>
                        <x-amani.badge ton="bronze">Nouveau</x-amani.badge>
                        <x-amani.badge ton="bronze">Édition limitée</x-amani.badge>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <x-amani.pastille-etat etat="succes">Livrée</x-amani.pastille-etat>
                        <x-amani.pastille-etat etat="attention">En attente de livraison</x-amani.pastille-etat>
                        <x-amani.pastille-etat etat="alerte">Rupture</x-amani.pastille-etat>
                        <x-amani.pastille-etat>Reçue</x-amani.pastille-etat>
                    </div>
                </div>
            </div>
        </section>

        {{-- Catégories --}}
        <section data-apparition>
            <x-amani.titre-section titre="Cartes de catégorie" lien="#" />
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <x-amani.carte-categorie titre="Gloss" texte="Des lèvres douces et brillantes" url="#" />
                <x-amani.carte-categorie titre="Skincare" texte="Une peau éclatante et en bonne santé" url="#" />
                <x-amani.carte-categorie titre="Maquillage" texte="Pour un teint parfait en toutes occasions" url="#" />
            </div>
        </section>

        {{-- Vignettes --}}
        <section data-apparition>
            <x-amani.titre-section titre="Nos meilleures ventes" lien="#" />
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                <x-amani.vignette-produit nom="Gloss Amani - Pink Diamond" :prix="7000" url="#" :badge="['texte' => 'Best-seller', 'ton' => 'cacao']" :note="4.7" :avis="74" />
                <x-amani.vignette-produit nom="Gommage lèvres - Fraise" :prix="4000" url="#" :badge="['texte' => 'Best-seller', 'ton' => 'cacao']" :note="4.6" :avis="67" />
                <x-amani.vignette-produit nom="Sérum éclat" :prix="5400" :prix-barre="6000" url="#" :badge="['texte' => 'Promo', 'ton' => 'rouge']" :note="4.7" :avis="56" />
                <x-amani.vignette-produit nom="Rouge à lèvres - Brown Glam" :prix="5000" url="#" :badge="['texte' => 'Nouveau', 'ton' => 'bronze']" />
            </div>
        </section>

        {{-- Commande --}}
        <section data-apparition>
            <x-amani.titre-section titre="Parcours de commande" />
            <div class="mt-8 flex flex-col gap-8 rounded-bloc bg-blanc p-6 shadow-carte sm:p-8">
                <x-amani.etapes :courante="2" />
                <div class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
                    <ul class="rounded-carte border border-filet p-5">
                        <x-amani.ligne-panier nom="Gloss Amani - Nude Glow" teinte="Nude Glow" :quantite="1" :prix="5000" />
                        <x-amani.ligne-panier nom="Baume à lèvres - Vanille" :quantite="2" :prix="4000" />
                        <x-amani.ligne-panier nom="Crème solaire SPF 50" :quantite="1" :prix="6000" />
                    </ul>
                    <div class="flex flex-col gap-5 rounded-carte bg-creme p-5">
                        <h3 class="titre text-2xl text-brun">Résumé de la commande</h3>
                        <dl class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between"><dt class="text-taupe">Sous-total (3 produits)</dt><dd><x-amani.prix :montant="19000" /></dd></div>
                            <div class="flex justify-between"><dt class="text-taupe">Frais de livraison</dt><dd class="text-taupe">À calculer</dd></div>
                            <div class="mt-2 flex items-baseline justify-between border-t border-filet pt-3"><dt class="titre text-xl text-brun">Total estimé</dt><dd><x-amani.prix :montant="19000" class="text-xl" /></dd></div>
                        </dl>
                        <x-amani.bouton :pleine-largeur="true" icone="cadenas" type="button">Passer à la livraison</x-amani.bouton>
                        <div class="rounded-champ bg-blanc p-4">
                            <p class="mb-2 flex items-center gap-2 text-sm font-semibold text-brun"><x-amani.icone nom="cadeau" :taille="20" class="text-bronze" /> Programme VIP</p>
                            <x-amani.barre-progression :valeur="19000" :maximum="50000" libelle="Progression vers le premier cadeau VIP">Plus que 31 000 FCFA pour bénéficier de 1 gloss VIP.</x-amani.barre-progression>
                        </div>
                    </div>
                </div>
                <div>
                    <x-amani.bouton variante="contour" type="button" x-data x-on:click="$dispatch('ouvrir-panier-exemple')">Ouvrir le tiroir du panier</x-amani.bouton>
                </div>
            </div>

            <x-amani.tiroir-panier evenement="ouvrir-panier-exemple">
                <x-amani.ligne-panier nom="Gloss Amani - Nude Glow" teinte="Nude Glow" :quantite="1" :prix="5000" />
                <x-amani.ligne-panier nom="Baume à lèvres - Vanille" :quantite="2" :prix="4000" />
                <x-slot:pied>
                    <div class="mb-5 flex items-baseline justify-between">
                        <span class="text-taupe">{{ __('vitrine.panier.sous_total') }}</span>
                        <x-amani.prix :montant="13000" class="text-xl" />
                    </div>
                    <x-amani.bouton :pleine-largeur="true" href="#">{{ __('vitrine.panier.voir') }}</x-amani.bouton>
                </x-slot:pied>
            </x-amani.tiroir-panier>
        </section>

        {{-- Tableau --}}
        <section data-apparition>
            <x-amani.titre-section titre="Tableau" />
            <x-amani.tableau class="mt-8" legende="Exemple de commandes" :colonnes="['Commande', 'Date', 'Zone', 'Statut', ['libelle' => 'Total', 'nombre' => true]]">
                <tr>
                    <td class="chiffres font-medium">001</td><td>17/10/2026</td><td>Plateau</td>
                    <td><x-amani.pastille-etat>Reçue</x-amani.pastille-etat></td><td class="nombre">{{ \App\Support\Montant::fcfa(16500) }}</td>
                </tr>
                <tr>
                    <td class="chiffres font-medium">002</td><td>17/10/2026</td><td>Almadies</td>
                    <td><x-amani.pastille-etat etat="attention">En attente de livraison</x-amani.pastille-etat></td><td class="nombre">{{ \App\Support\Montant::fcfa(9000) }}</td>
                </tr>
                <tr>
                    <td class="chiffres font-medium">003</td><td>16/10/2026</td><td>Parcelles</td>
                    <td><x-amani.pastille-etat etat="succes">Livrée</x-amani.pastille-etat></td><td class="nombre">{{ \App\Support\Montant::fcfa(5000) }}</td>
                </tr>
            </x-amani.tableau>
        </section>
    </div>
</x-layouts.vitrine>
