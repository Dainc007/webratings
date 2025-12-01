<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirHumidifierResource\Pages;

use App\Filament\Resources\AirHumidifierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListAirHumidifiers extends ListRecords
{
    protected static string $resource = AirHumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
