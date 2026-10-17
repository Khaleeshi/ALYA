<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdministrationPanelProvider;
use App\Providers\HorizonServiceProvider;

return [
    AppServiceProvider::class,
    AdministrationPanelProvider::class,
    HorizonServiceProvider::class,
];
