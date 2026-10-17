{{--
    Libellé d'un champ : astérisque pour un champ obligatoire, « (facultatif) »
    sinon, comme sur la maquette 5.
--}}
@props(['pour', 'requis' => false])

<label for="{{ $pour }}" {{ $attributes->class('text-sm font-medium text-brun') }}>
    {{ $slot }}
    @if ($requis)
        <span class="text-rouge" aria-hidden="true">*</span>
    @else
        <span class="font-normal text-taupe">({{ __('vitrine.facultatif') }})</span>
    @endif
</label>
