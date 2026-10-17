<?php

namespace App\Providers;

use App\Support\Acces\Permissions;
use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Le tableau de bord des files (/administration/files) est réservé au
     * personnel qui a la permission de supervision technique. En local, il
     * reste ouvert pour le développement.
     */
    protected function authorization(): void
    {
        Horizon::auth(function (Request $request): bool {
            if ($this->app->environment('local')) {
                return true;
            }

            return (bool) $request->user('personnel')?->can(Permissions::TECHNIQUE_SUPERVISER);
        });
    }

    protected function gate(): void
    {
        // Remplacé par authorization() ci-dessus.
    }
}
