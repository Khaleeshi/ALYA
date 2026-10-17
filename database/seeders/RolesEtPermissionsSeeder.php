<?php

namespace Database\Seeders;

use App\Support\Acces\Permissions;
use App\Support\Acces\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crée les rôles et permissions du personnel. Peut être relancé sans risque :
 * il ajoute ce qui manque et ne retire rien de ce qui a été accordé à la main.
 */
class RolesEtPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::toutes() as $permission) {
            Permission::findOrCreate($permission, Roles::GARDE);
        }

        foreach (array_keys(Roles::libelles()) as $nom) {
            $role = Role::findOrCreate($nom, Roles::GARDE);
            $role->givePermissionTo(Permissions::PAR_ROLE[$nom] ?? []);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
