<?php

namespace App\Filament\Resources\Gammes\Pages;

use App\Filament\Resources\Gammes\GammeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGammes extends ManageRecords
{
    protected static string $resource = GammeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
