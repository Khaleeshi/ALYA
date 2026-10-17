<?php

namespace App\Exceptions;

use LogicException;

/**
 * Levée à toute tentative de modifier ou de supprimer une ligne du journal.
 */
class JournalImmuable extends LogicException
{
    public function __construct()
    {
        parent::__construct("Le journal d'activité est en ajout seul : une ligne ne se modifie ni ne se supprime.");
    }
}
