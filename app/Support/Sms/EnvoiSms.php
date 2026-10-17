<?php

namespace App\Support\Sms;

/**
 * Envoi d'un SMS. Chaque prestataire (à choisir) aura sa propre classe ;
 * le reste du site ne connaît que cette interface.
 */
interface EnvoiSms
{
    /**
     * @param  string  $telephone  numéro au format international (+221…)
     */
    public function envoyer(string $telephone, string $message): void;
}
