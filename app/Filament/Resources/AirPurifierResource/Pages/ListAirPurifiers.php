<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirPurifierResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AirPurifierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListAirPurifiers extends ListRecords
{
    protected static string $resource = AirPurifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
