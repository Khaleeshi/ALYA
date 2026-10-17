<?php

use App\Support\Telephone;

it('enregistre un numéro sénégalais au format international, quelle que soit la saisie', function (string $saisie) {
    expect(Telephone::normaliser($saisie))->toBe('+221771234567');
})->with([
    '77 123 45 67',
    '771234567',
    '+221 77 123 45 67',
    '00221771234567',
    '221771234567',
    '77.123.45.67',
]);

it('accepte les numéros étrangers de la diaspora', function () {
    expect(Telephone::normaliser('+33 6 12 34 56 78'))->toBe('+33612345678');
});

it('refuse les numéros incomplets ou impossibles', function (?string $saisie) {
    expect(Telephone::normaliser($saisie))->toBeNull();
})->with([
    '77 12',
    '12345',
    '+221 99 123 45 67',
    '+221 77 123 45',
    'bonjour',
    '',
    null,
]);

it('affiche un numéro sénégalais par groupes de chiffres', function () {
    expect(Telephone::afficher('+221771234567'))->toBe('+221 77 123 45 67');
});
