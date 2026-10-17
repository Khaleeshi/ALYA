<?php

namespace App\Support;

/**
 * Numéros de téléphone au format international (décision D10).
 *
 * Une cliente peut saisir « 77 123 45 67 », « 00221771234567 » ou
 * « +221 77 123 45 67 » : le site enregistre toujours « +221771234567 ».
 * Les numéros étrangers (diaspora) sont acceptés s'ils commencent par « + ».
 */
final class Telephone
{
    public const INDICATIF_SENEGAL = '+221';

    /**
     * Retourne le numéro au format international, ou null s'il n'est pas valable.
     */
    public static function normaliser(?string $saisie): ?string
    {
        if ($saisie === null) {
            return null;
        }

        $numero = preg_replace('/[^\d+]/', '', $saisie);

        if (str_starts_with($numero, '00')) {
            $numero = '+'.substr($numero, 2);
        }

        if (! str_starts_with($numero, '+')) {
            if (preg_match('/^221([37]\d{8})$/', $numero, $morceaux)) {
                $numero = $morceaux[1];
            }

            if (! preg_match('/^[37]\d{8}$/', $numero)) {
                return null;
            }

            $numero = self::INDICATIF_SENEGAL.$numero;
        }

        if (str_starts_with($numero, self::INDICATIF_SENEGAL)) {
            // Mobiles 70 à 78, fixes 30 et 33.
            return preg_match('/^\+221(7[0-8]|3[03])\d{7}$/', $numero) ? $numero : null;
        }

        return preg_match('/^\+[1-9]\d{7,14}$/', $numero) ? $numero : null;
    }

    /**
     * « +221771234567 » devient « +221 77 123 45 67 ».
     */
    public static function afficher(string $numero): string
    {
        if (preg_match('/^\+221(\d{2})(\d{3})(\d{2})(\d{2})$/', $numero, $m)) {
            return "+221 {$m[1]} {$m[2]} {$m[3]} {$m[4]}";
        }

        return $numero;
    }
}
