<?php

namespace App\Support;

/**
 * Affichage des montants. Le FCFA (XOF) est la seule monnaie de paiement ;
 * les montants sont toujours des entiers (le FCFA n'a pas de centimes).
 */
final class Montant
{
    /**
     * 4500 devient « 4 500 FCFA » en français, « 4,500 FCFA » en anglais.
     */
    public static function fcfa(int $montant, ?string $langue = null): string
    {
        $langue ??= app()->getLocale();
        $separateurMilliers = $langue === 'en' ? ',' : "\u{202F}";

        return number_format($montant, 0, '', $separateurMilliers)."\u{00A0}FCFA";
    }
}
