<?php

namespace App\Listeners;

use App\Models\Cliente;
use App\Support\Panier\GestionPanier;
use Illuminate\Auth\Events\Login;

/**
 * Quand une cliente se connecte (ou crée son compte), le panier qu'elle a
 * rempli sans être connectée rejoint celui de son compte.
 */
class FusionnerPanier
{
    public function __construct(private readonly GestionPanier $panier) {}

    public function handle(Login $evenement): void
    {
        if ($evenement->guard === 'web' && $evenement->user instanceof Cliente) {
            $this->panier->fusionnerDans($evenement->user);
        }
    }
}
