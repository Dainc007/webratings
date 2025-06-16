<?php

namespace App\Filament\Resources\AirHumidifierResource\Pages;

use App\Filament\Resources\AirHumidifierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAirHumidifier extends EditRecord
{
    protected static string $resource = AirHumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
