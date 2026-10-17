{{--
    Collections (cahier §3, maquette 3) : les catégories en grandes cartes,
    puis les collections permanentes, saisonnières, les éditions limitées et
    les coffrets cadeaux. Une section vide n'est pas affichée.
--}}
@use('App\Models\Categorie')
@use('App\Models\Gamme')
@use('App\Models\Produit')

<x-layouts.vitrine :titre="__('vitrine.collections.titre')" :description="__('vitrine.collections.sous_titre')">
    <x-amani.bandeau-page :titre="__('vitrine.collections.titre')" :sous-titre="__('vitrine.collections.sous_titre')" />

    <div class="conteneur flex flex-col gap-14 py-10 lg:gap-16 lg:py-14">
        @if ($categories->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($categories as $categorie)
                    <x-amani.carte-categorie
                        :titre="__('vitrine.collections.categorie', ['nom' => $categorie->nom])"
                        :texte="$categorie->description"
                        :url="route_langue('boutique.categorie', ['categorie' => $categorie->slug])"
                        :photo="$categorie->getFirstMedia(Categorie::COUVERTURE)"
                    />
                @endforeach
            </div>
        @endif

        @foreach (['permanentes' => $permanentes, 'saisonnieres' => $saisonnieres] as $cle => $gammes)
            @if ($gammes->isNotEmpty())
                <section>
                    <x-amani.titre-section :titre="__('vitrine.collections.'.$cle)" />
                    <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($gammes as $gamme)
                            <li class="flex">
                                <x-amani.carte-collection
                                    :titre="$gamme->nom"
                                    :texte="$gamme->accroche"
                                    :url="route_langue('collections.gamme', ['gamme' => $gamme->slug])"
                                    :photo="$gamme->getFirstMedia(Gamme::COUVERTURE)"
                                    class="w-full"
                                />
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @endforeach

        @if ($limitees->isNotEmpty())
            <section>
                <x-amani.titre-section :titre="__('vitrine.collections.limitees')" :lien="route_langue('editions-limitees')" />
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    @foreach ($limitees as $gamme)
                        <x-amani.carte-categorie
                            :titre="$gamme->nom"
                            :texte="$gamme->accroche"
                            :url="route_langue('collections.gamme', ['gamme' => $gamme->slug])"
                            :photo="$gamme->getFirstMedia(Gamme::COUVERTURE)"
                            :badge="__('vitrine.produit.edition_limitee')"
                            class="min-h-56"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($coffrets->isNotEmpty())
            <section>
                <x-amani.titre-section :titre="__('vitrine.collections.coffrets')" :lien="route_langue('coffrets')" />
                <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($coffrets as $coffret)
                        <li class="flex">
                            <x-amani.carte-collection
                                :titre="$coffret->nom"
                                :texte="$coffret->accroche"
                                :url="route_langue('boutique.produit', ['produit' => $coffret->slug])"
                                :photo="$coffret->getFirstMedia(Produit::PHOTOS)"
                                :action="__('vitrine.coffrets.voir')"
                                class="w-full"
                            />
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($categories->isEmpty() && $permanentes->isEmpty() && $saisonnieres->isEmpty() && $limitees->isEmpty() && $coffrets->isEmpty())
            <x-amani.notice>{{ __('vitrine.collections.bientot') }}</x-amani.notice>
        @endif
    </div>
</x-layouts.vitrine>
