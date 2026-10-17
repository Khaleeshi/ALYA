<?php

namespace App\Filament\Resources\Effets\Pages;

use App\Filament\Resources\Effets\EffetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEffets extends ManageRecords
{
    protected static string $resource = EffetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
