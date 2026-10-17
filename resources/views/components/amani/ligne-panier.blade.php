{{--
    Ligne du panier (maquette 4) : photo, nom, couleur, disponibilité, prix,
    quantité en pilule, favori et suppression.

    Sans urlModifier / urlRetirer, la ligne n'est qu'une présentation
    (bibliothèque de composants). Avec, « − », « + » et la corbeille sont
    de vrais formulaires : ils marchent sans JavaScript.
    quantiteMax : le « + » s'arrête là (stock et limite par commande).
    disponible = false : article épuisé ou retiré, hors du total.
--}}
@props([
    'nom',
    'quantite',
    'prix',
    'prixBarre' => null,
    'teinte' => null,
    'photo' => null,
    'disponible' => true,
    'url' => null,
    'urlModifier' => null,
    'urlRetirer' => null,
    'quantiteMax' => null,
])

@php
    $bouton = 'inline-flex size-9 items-center justify-center text-brun transition-colors hover:text-bronze-fonce disabled:cursor-not-allowed disabled:text-taupe/40';
@endphp

<li {{ $attributes->class(['flex gap-4 border-b border-filet py-5 first:pt-0 last:border-b-0', 'opacity-70' => ! $disponible]) }}>
    <div class="size-20 shrink-0 overflow-hidden rounded-champ bg-nude-pale sm:size-24">
        @if ($photo)
            <x-amani.image :media="$photo" alt="" sizes="96px" class="size-full object-cover" />
        @endif
    </div>

    <div class="flex min-w-0 flex-1 flex-col gap-1">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[15px] leading-snug font-medium text-brun">
                    @if ($url)
                        <a href="{{ $url }}" class="hover:text-bronze-fonce">{{ $nom }}</a>
                    @else
                        {{ $nom }}
                    @endif
                </p>
                @if ($teinte)
                    <p class="text-sm text-taupe">{{ __('vitrine.panier.teinte') }} : {{ $teinte }}</p>
                @endif
                @if ($disponible)
                    <p class="mt-0.5 flex items-center gap-1.5 text-sm text-vert">
                        <span class="size-1.5 rounded-full bg-vert" aria-hidden="true"></span>{{ __('vitrine.panier.en_stock') }}
                    </p>
                @else
                    <p class="mt-0.5 flex items-center gap-1.5 text-sm text-rouge">
                        <span class="size-1.5 rounded-full bg-rouge" aria-hidden="true"></span>{{ __('vitrine.panier.plus_disponible') }}
                    </p>
                @endif
            </div>
            <div class="shrink-0 text-right">
                <x-amani.prix :montant="$prix * $quantite" :barre="$prixBarre ? $prixBarre * $quantite : null" class="text-[15px]" />
                @if ($quantite > 1)
                    <p class="chiffres text-xs text-taupe">{{ __('vitrine.panier.prix_unitaire', ['prix' => \App\Support\Montant::fcfa($prix)]) }}</p>
                @endif
            </div>
        </div>

        <div class="mt-auto flex items-center justify-between pt-2">
            @if ($disponible)
                <div class="flex items-center rounded-full border border-filet bg-blanc" role="group" aria-label="{{ __('vitrine.panier.quantite') }} : {{ $nom }}">
                    @if ($urlModifier)
                        <form method="post" action="{{ $urlModifier }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="quantite" value="{{ $quantite - 1 }}">
                            <button type="submit" class="{{ $bouton }}" aria-label="{{ __('vitrine.panier.diminuer') }}" @disabled($quantite <= 1)>
                                <x-amani.icone nom="moins" :taille="16" />
                            </button>
                        </form>
                    @else
                        <button type="button" class="{{ $bouton }}" aria-label="{{ __('vitrine.panier.diminuer') }}"><x-amani.icone nom="moins" :taille="16" /></button>
                    @endif

                    <span class="chiffres w-7 text-center text-sm font-medium">{{ $quantite }}</span>

                    @if ($urlModifier)
                        <form method="post" action="{{ $urlModifier }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="quantite" value="{{ $quantite + 1 }}">
                            <button type="submit" class="{{ $bouton }}" aria-label="{{ __('vitrine.panier.augmenter') }}" @disabled($quantiteMax !== null && $quantite >= $quantiteMax)>
                                <x-amani.icone nom="plus" :taille="16" />
                            </button>
                        </form>
                    @else
                        <button type="button" class="{{ $bouton }}" aria-label="{{ __('vitrine.panier.augmenter') }}"><x-amani.icone nom="plus" :taille="16" /></button>
                    @endif
                </div>
            @else
                <span></span>
            @endif

            <div class="flex items-center gap-1">
                @if (Route::has(app()->getLocale().'.mes-favoris'))
                    <button type="button" class="inline-flex size-9 items-center justify-center text-taupe hover:text-bronze-fonce" aria-label="{{ __('vitrine.ajouter_aux_favoris', ['nom' => $nom]) }}">
                        <x-amani.icone nom="coeur" :taille="20" />
                    </button>
                @endif
                @if ($urlRetirer)
                    <form method="post" action="{{ $urlRetirer }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="inline-flex size-9 items-center justify-center text-taupe hover:text-rouge" aria-label="{{ __('vitrine.panier.retirer') }} : {{ $nom }}">
                            <x-amani.icone nom="corbeille" :taille="20" />
                        </button>
                    </form>
                @else
                    <button type="button" class="inline-flex size-9 items-center justify-center text-taupe hover:text-rouge" aria-label="{{ __('vitrine.panier.retirer') }}">
                        <x-amani.icone nom="corbeille" :taille="20" />
                    </button>
                @endif
            </div>
        </div>
    </div>
</li>
