<?php

namespace Tests;

use Database\Seeders\RolesEtPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Les rôles et permissions existent dans chaque test qui utilise la base. */
    protected bool $seed = true;

    protected string $seeder = RolesEtPermissionsSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        // Les pages se rendent sans les fichiers compilés par Vite.
        $this->withoutVite();
    }
}
