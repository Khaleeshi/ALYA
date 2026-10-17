<?php

namespace App\Support\Catalogue;

use Illuminate\Support\Str;

/**
 * Recherche sans souci d'accents ni de majuscules : « creme eclat »
 * trouve « Crème Éclat ». Le texte des produits et la saisie de la
 * cliente passent par la même normalisation.
 */
final class TexteRecherche
{
    public static function normaliser(?string $texte): string
    {
        $texte = Str::lower(Str::ascii((string) $texte));

        return trim((string) preg_replace('/[^a-z0-9]+/', ' ', $texte));
    }

    /**
     * Mots d'une recherche, sans les lettres isolées ni les doublons.
     *
     * @return list<string>
     */
    public static function mots(?string $saisie): array
    {
        $mots = explode(' ', self::normaliser($saisie));

        return array_values(array_unique(array_filter($mots, fn (string $mot): bool => mb_strlen($mot) >= 2)));
    }
}
