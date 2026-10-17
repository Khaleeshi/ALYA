{{-- Message d'erreur sous un champ. --}}
@props(['id', 'message'])

<p id="{{ $id }}" {{ $attributes->class('flex items-start gap-1.5 text-sm text-rouge') }}>
    <x-amani.icone nom="alerte" :taille="16" class="mt-0.5" />
    <span>{{ $message }}</span>
</p>
