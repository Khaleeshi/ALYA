<?php

use App\Models\Cliente;
use App\Models\CodeConnexion;
use App\Models\Consentement;
use App\Models\JournalActivite;
use App\Support\Comptes\CodesConnexion;
use App\Support\Securite\VerrouillageConnexion;
use App\Support\Sms\SmsJournal;

beforeEach(function () {
    $this->freezeSecond();
    $this->sms = smsFactice();
});

function demanderCode(string $telephone = '77 123 45 67')
{
    return test()->from('/fr/connexion')->post('/fr/connexion', ['telephone' => $telephone]);
}

function saisirCode(string $code, bool $souvenir = false)
{
    return test()->from('/fr/connexion/code')->post('/fr/connexion/code', array_filter(['code' => $code, 'souvenir' => $souvenir ? '1' : null]));
}

it('envoie un code à six chiffres par SMS et ne garde que son empreinte', function () {
    demanderCode('77 123 45 67')->assertRedirect('/fr/connexion/code');

    $code = $this->sms->dernierCode('+221771234567');

    expect($code)->toMatch('/^\d{6}$/')
        ->and($this->sms->envoyes[0]['message'])->toContain('AMANI Cosmetics')
        ->and(CodeConnexion::sole()->empreinte)->not->toContain($code)
        ->and(CodeConnexion::sole()->expire_le->diffInMinutes(now(), true))->toEqual(CodesConnexion::VALIDITE_MINUTES);
});

it('refuse un numéro étranger ou impossible : le site vend au Sénégal', function (string $telephone) {
    demanderCode($telephone)->assertSessionHasErrors(['telephone' => __('validation.telephone_senegal')]);

    expect($this->sms->envoyes)->toBe([]);
})->with(['+33 6 12 34 56 78', '77 12', 'bonjour']);

it('connecte une cliente existante avec le bon code', function () {
    $cliente = Cliente::factory()->create(['telephone' => '+221771234567']);

    demanderCode('00221 77 123 45 67');
    saisirCode($this->sms->dernierCode('+221771234567'))->assertRedirect('/fr/mon-compte');

    $this->assertAuthenticatedAs($cliente, 'web');
});

it('accepte un code tapé avec des espaces', function () {
    Cliente::factory()->create(['telephone' => '+221771234567']);

    demanderCode();
    $code = $this->sms->dernierCode('+221771234567');
    saisirCode(substr($code, 0, 3).' '.substr($code, 3))->assertRedirect('/fr/mon-compte');
});

it('refuse un code faux, puis un code déjà utilisé', function () {
    $cliente = Cliente::factory()->create(['telephone' => '+221771234567']);

    demanderCode();
    $code = $this->sms->dernierCode('+221771234567');

    saisirCode($code === '000000' ? '111111' : '000000')->assertSessionHasErrors(['code' => __('comptes.code.invalide')]);
    $this->assertGuest('web');

    saisirCode($code)->assertRedirect('/fr/mon-compte');
    $this->post('/fr/deconnexion');

    $this->withSession(['connexion.telephone' => '+221771234567']);
    saisirCode($code)->assertSessionHasErrors(['code' => __('comptes.code.invalide')]);
});

it('refuse un code expiré au bout de dix minutes', function () {
    Cliente::factory()->create(['telephone' => '+221771234567']);

    demanderCode();
    $code = $this->sms->dernierCode('+221771234567');

    $this->travel(CodesConnexion::VALIDITE_MINUTES)->minutes();
    $this->travel(1)->second();

    saisirCode($code)->assertSessionHasErrors(['code' => __('comptes.code.invalide')]);
    $this->assertGuest('web');
});

it('rend le code précédent caduc quand un nouveau est envoyé', function () {
    Cliente::factory()->create(['telephone' => '+221771234567']);

    demanderCode();
    $premier = $this->sms->dernierCode('+221771234567');

    $this->travel(CodesConnexion::DELAI_RENVOI_SECONDES + 1)->seconds();
    $this->from('/fr/connexion/code')->post('/fr/connexion/code/renvoyer')->assertSessionHasNoErrors();
    $second = $this->sms->dernierCode('+221771234567');

    if ($premier !== $second) {
        saisirCode($premier)->assertSessionHasErrors('code');
    }

    saisirCode($second)->assertRedirect('/fr/mon-compte');
});

it("impose une minute d'attente entre deux codes et trois codes au plus par quart d'heure", function () {
    demanderCode();

    $this->from('/fr/connexion/code')->post('/fr/connexion/code/renvoyer')
        ->assertSessionHasErrors(['code' => __('comptes.code.attendre', ['secondes' => CodesConnexion::DELAI_RENVOI_SECONDES])]);

    foreach (range(2, CodesConnexion::ENVOIS_PAR_NUMERO) as $envoi) {
        $this->travel(CodesConnexion::DELAI_RENVOI_SECONDES + 1)->seconds();
        $this->from('/fr/connexion/code')->post('/fr/connexion/code/renvoyer')->assertSessionHasNoErrors();
    }

    $this->travel(CodesConnexion::DELAI_RENVOI_SECONDES + 1)->seconds();
    $this->from('/fr/connexion/code')->post('/fr/connexion/code/renvoyer')->assertSessionHasErrors('code');

    expect($this->sms->envoyes)->toHaveCount(CodesConnexion::ENVOIS_PAR_NUMERO);
});

it('verrouille la connexion après cinq codes faux, même avec le bon code ensuite', function () {
    Cliente::factory()->create(['telephone' => '+221771234567']);

    demanderCode();
    $bon = $this->sms->dernierCode('+221771234567');
    $faux = $bon === '000000' ? '111111' : '000000';

    foreach (range(1, VerrouillageConnexion::ESSAIS) as $essai) {
        saisirCode($faux);
    }

    saisirCode($bon)->assertSessionHasErrors(['code' => __('auth.throttle', ['seconds' => 900, 'minutes' => 15])]);
    $this->assertGuest('web');

    expect(JournalActivite::where('action', 'connexion.verrouillage')->sole()->donnees)
        ->toMatchArray(['espace' => 'boutique', 'identifiant' => '+221771234567']);
});

it('fait créer son compte à une nouvelle cliente une fois son numéro vérifié', function () {
    demanderCode('78 000 11 22');
    saisirCode($this->sms->dernierCode('+221780001122'), souvenir: true)->assertRedirect('/fr/inscription');

    $this->get('/fr/inscription')->assertOk()->assertSee('+221 78 000 11 22');

    $this->post('/fr/inscription', ['prenom' => 'Awa', 'nom' => 'Diop', 'email' => '', 'offres' => '1'])
        ->assertRedirect('/fr/mon-compte');

    $cliente = Cliente::sole();

    expect($cliente->telephone)->toBe('+221780001122')
        ->and($cliente->telephone_verifie_le)->not->toBeNull()
        ->and($cliente->email)->toBeNull()
        ->and($cliente->aConsenti(Consentement::OFFRES))->toBeTrue()
        ->and(JournalActivite::where('action', 'cliente.inscription')->sole()->auteur_libelle)->toBe('Cliente · Awa');

    $this->assertAuthenticatedAs($cliente, 'web');
});

it("refuse la création d'un compte sans numéro vérifié", function () {
    $this->get('/fr/inscription')->assertRedirect('/fr/connexion');
    $this->post('/fr/inscription', ['prenom' => 'Awa', 'nom' => 'Diop'])->assertRedirect('/fr/connexion');

    expect(Cliente::count())->toBe(0);
});

it('laisse trente minutes pour compléter le compte après la vérification', function () {
    demanderCode('78 000 11 22');
    saisirCode($this->sms->dernierCode('+221780001122'));

    $this->travel(31)->minutes();

    $this->get('/fr/inscription')->assertRedirect('/fr/connexion');
});

it('refuse un email déjà utilisé par une autre cliente', function () {
    Cliente::factory()->create(['email' => 'awa@example.com']);

    demanderCode('78 000 11 22');
    saisirCode($this->sms->dernierCode('+221780001122'));

    $this->post('/fr/inscription', ['prenom' => 'Awa', 'nom' => 'Diop', 'email' => 'Awa@Example.com'])
        ->assertSessionHasErrors('email');
});

it('déconnecte la cliente et la ramène à l\'accueil', function () {
    $this->actingAs(Cliente::factory()->create(), 'web')
        ->post('/fr/deconnexion')
        ->assertRedirect('/fr');

    $this->assertGuest('web');
});

it('envoie une visiteuse vers la connexion et une cliente connectée vers son compte', function () {
    $this->get('/fr/mon-compte')->assertRedirect('/fr/connexion');
    $this->get('/fr/connexion/code')->assertRedirect('/fr/connexion');

    $this->actingAs(Cliente::factory()->create(), 'web')
        ->get('/fr/connexion')
        ->assertRedirect('/fr/mon-compte');
});

it("affiche l'espace cliente avec ses coordonnées", function () {
    $cliente = Cliente::factory()->sansEmail()->create(['telephone' => '+221771234567']);

    $this->actingAs($cliente, 'web')
        ->get('/fr/mon-compte')
        ->assertOk()
        ->assertSee('+221 77 123 45 67')
        ->assertSee(__('comptes.mon_compte.non_renseigne'));
});

it('refuse de partir en production avec le SMS de démonstration', function () {
    app()->detectEnvironment(fn () => 'production');

    (new SmsJournal)->envoyer('+221771234567', 'Code 123456');
})->throws(RuntimeException::class);
