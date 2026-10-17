{{--
    Question qui se déplie (maquette 7 : questions fréquentes ; fiche produit :
    ingrédients, conseils). Fonctionne sans JavaScript.
--}}
@props(['question', 'ouvert' => false])

<details {{ $attributes->class('group rounded-champ border border-filet bg-blanc') }} @if ($ouvert) open @endif>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 text-[15px] font-medium text-brun [&::-webkit-details-marker]:hidden">
        <span>{{ $question }}</span>
        <x-amani.icone nom="chevron-bas" :taille="18" class="text-taupe transition-transform duration-200 group-open:rotate-180" />
    </summary>
    <div class="px-5 pb-5 text-[15px] text-taupe">
        {{ $slot }}
    </div>
</details>
