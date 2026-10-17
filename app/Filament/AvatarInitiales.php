<?php

namespace App\Filament;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * Avatar du personnel : ses initiales en bronze sur nude, générées sur place.
 * Remplace le service externe de Filament (aucune donnée envoyée ailleurs,
 * aucune photo dans l'administration).
 */
class AvatarInitiales implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initiales = collect([$record->prenom ?? '', $record->nom ?? ''])
            ->filter()
            ->map(fn (string $morceau): string => mb_strtoupper(mb_substr($morceau, 0, 1)))
            ->implode('');

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                <rect width="64" height="64" fill="#f5e8d7"/>
                <text x="32" y="41" text-anchor="middle" font-family="Georgia, serif" font-size="24" letter-spacing="1" fill="#85552c">{$initiales}</text>
            </svg>
            SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
