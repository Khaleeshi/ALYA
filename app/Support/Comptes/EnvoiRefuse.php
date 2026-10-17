<?php

namespace App\Support\Comptes;

use RuntimeException;

/**
 * Un nouveau code ne peut pas partir tout de suite (renvoi trop rapproché ou
 * trop de demandes). Le message s'adresse directement à la cliente.
 */
class EnvoiRefuse extends RuntimeException {}
