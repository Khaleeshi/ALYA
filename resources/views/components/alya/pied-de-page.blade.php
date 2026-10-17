{{--
    Pied de page (audit 9.3) : quatre colonnes sobres (navigation, informations,
    contact, newsletter), moyens de paiement, réseaux, signature en italique.
    Pas de barre de réassurance. Une colonne sans contenu n'est pas affichée.

    Les moyens de paiement s'écrivent en toutes lettres tant que les fichiers de
    logos officiels de Wave et Orange Money ne sont pas fournis (jamais redessinés).
--}}
@props(['reseaux' => []])

@php
    $colonnes = [
        'navigation' => $navigation ?? null,
        'informations' => $informations ?? null,
        'contact' => $contact ?? null,
        'newsletter' => $newsletter ?? null,
    ];
    $colonnes = array_filter($colonnes, fn ($contenu) => $contenu !== null && ! $contenu->isEmpty());
@endphp

<footer {{ $attributes->class('border-t border-filet bg-noir') }}>
    <div class="conteneur py-16 lg:py-20">
        @if ($colonnes !== [])
            <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                @foreach ($colonnes as $cle => $contenu)
                    <section>
                        <h2 class="etiquette mb-5 text-gris">{{ __('vitrine.pied.'.$cle) }}</h2>
                        <div class="flex flex-col gap-3 text-sm text-ivoire [&_a:hover]:text-or">{{ $contenu }}</div>
                    </section>
                @endforeach
            </div>
        @endif

        <div @class(['flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between', 'mt-16 border-t border-filet pt-10' => $colonnes !== []])>
            <p class="signature text-2xl text-or-clair lg:text-3xl">{{ __('vitrine.signature') }}</p>

            <div class="flex flex-col gap-6 sm:flex-row sm:gap-12">
                <div>
                    <h2 class="etiquette mb-3 text-gris">{{ __('vitrine.pied.paiement') }}</h2>
                    <ul class="flex gap-4 text-sm text-ivoire">
                        <li>Wave</li>
                        <li>Orange Money</li>
                    </ul>
                </div>

                @if (count($reseaux) > 0)
                    <div>
                        <h2 class="etiquette mb-3 text-gris">{{ __('vitrine.pied.reseaux') }}</h2>
                        <ul class="flex gap-4 text-sm text-ivoire">
                            @foreach ($reseaux as $reseau)
                                <li><a href="{{ $reseau['url'] }}" class="hover:text-or" rel="noopener" target="_blank">{{ $reseau['nom'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <p class="mt-10 text-xs text-gris">{{ __('vitrine.pied.droits', ['annee' => now()->year]) }}</p>
    </div>
</footer>
