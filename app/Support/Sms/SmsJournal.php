<?php

namespace App\Support\Sms;

use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Pilote d'attente : le SMS est écrit dans le journal de l'application
 * (storage/logs) au lieu de partir. Il permet d'essayer la connexion en
 * développement et en recette avant le choix du prestataire ; en recette, il
 * peut aussi l'afficher à l'écran (SMS_AFFICHER_EN_RECETTE). En production,
 * il refuse d'envoyer : aucune fonction de démonstration ne doit y rester
 * (cahier §13 et §17).
 */
class SmsJournal implements EnvoiSms
{
    public function envoyer(string $telephone, string $message): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException("Aucun prestataire SMS n'est configuré : les codes de connexion ne peuvent pas partir en production.");
        }

        Log::info('SMS non envoyé (pilote « journal »)', ['telephone' => $telephone, 'message' => $message]);

        if (config('services.sms.afficher_en_recette')) {
            session()->flash('recette.sms', $message);
        }
    }
}
