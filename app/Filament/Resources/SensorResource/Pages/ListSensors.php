<?php

declare(strict_types=1);

namespace App\Filament\Resources\SensorResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SensorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListSensors extends ListRecords
{
    protected static string $resource = SensorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
