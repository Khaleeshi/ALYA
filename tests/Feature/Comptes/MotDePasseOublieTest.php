<?php

use App\Models\Cliente;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('donne le même message que le compte existe ou non', function () {
    Notification::fake();
    Cliente::factory()->create(['email' => 'awa@example.com']);

    $this->from('/fr/mot-de-passe-oublie')
        ->post('/fr/mot-de-passe-oublie', ['email' => 'awa@example.com'])
        ->assertRedirect('/fr/mot-de-passe-oublie')
        ->assertSessionHas('statut', __('comptes.lien_envoye'))
        ->assertSessionHasNoErrors();

    $this->from('/fr/mot-de-passe-oublie')
        ->post('/fr/mot-de-passe-oublie', ['email' => 'inconnue@example.com'])
        ->assertRedirect('/fr/mot-de-passe-oublie')
        ->assertSessionHas('statut', __('comptes.lien_envoye'))
        ->assertSessionHasNoErrors();
});

it('envoie un lien dans la langue de la cliente', function () {
    Notification::fake();
    $cliente = Cliente::factory()->anglophone()->create(['email' => 'awa@example.com']);

    $this->post('/fr/mot-de-passe-oublie', ['email' => 'awa@example.com']);

    Notification::assertSentTo($cliente, ResetPassword::class, function (ResetPassword $notification) use ($cliente) {
        $url = $notification->toMail($cliente)->actionUrl;

        return str_contains($url, '/en/reset-password/');
    });
});

it('change le mot de passe avec un lien valable puis renvoie vers la connexion', function () {
    $cliente = Cliente::factory()->create(['email' => 'awa@example.com']);
    $jeton = Password::broker('clientes')->createToken($cliente);

    $this->get('/fr/nouveau-mot-de-passe/'.$jeton.'?email=awa@example.com')->assertOk();

    $this->post('/fr/nouveau-mot-de-passe', [
        'token' => $jeton,
        'email' => 'awa@example.com',
        'password' => 'nouvelleBrillance1',
        'password_confirmation' => 'nouvelleBrillance1',
    ])->assertRedirect('/fr/connexion')->assertSessionHas('statut', __('comptes.mot_de_passe_change'));

    expect(Hash::check('nouvelleBrillance1', $cliente->fresh()->password))->toBeTrue();
});
