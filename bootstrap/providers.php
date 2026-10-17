<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdministrationPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HorizonServiceProvider;

return [
    AppServiceProvider::class,
    AdministrationPanelProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
];
