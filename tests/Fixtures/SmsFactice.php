<?php

namespace Tests\Fixtures;

use App\Support\Sms\EnvoiSms;

/**
 * Remplace le prestataire SMS dans les tests : les messages sont gardés en
 * mémoire pour lire le code reçu.
 */
class SmsFactice implements EnvoiSms
{
    /** @var list<array{telephone: string, message: string}> */
    public array $envoyes = [];

    public function envoyer(string $telephone, string $message): void
    {
        $this->envoyes[] = ['telephone' => $telephone, 'message' => $message];
    }

    public function dernierCode(string $telephone): ?string
    {
        foreach (array_reverse($this->envoyes) as $sms) {
            if ($sms['telephone'] === $telephone && preg_match('/\b(\d{6})\b/', $sms['message'], $morceaux)) {
                return $morceaux[1];
            }
        }

        return null;
    }
}
