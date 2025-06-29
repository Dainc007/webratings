<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirHumidifierResource\Pages;

use App\Filament\Resources\AirHumidifierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditAirHumidifier extends EditRecord
{
    protected static string $resource = AirHumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
