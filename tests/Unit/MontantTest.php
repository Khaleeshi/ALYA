<?php

use App\Support\Montant;

it('écrit les montants en FCFA avec une espace fine en français', function () {
    expect(Montant::fcfa(4500, 'fr'))->toBe("4\u{202F}500\u{00A0}FCFA")
        ->and(Montant::fcfa(100000, 'fr'))->toBe("100\u{202F}000\u{00A0}FCFA")
        ->and(Montant::fcfa(900, 'fr'))->toBe("900\u{00A0}FCFA");
});

it('écrit les montants avec une virgule en anglais', function () {
    expect(Montant::fcfa(13050, 'en'))->toBe("13,050\u{00A0}FCFA");
});
