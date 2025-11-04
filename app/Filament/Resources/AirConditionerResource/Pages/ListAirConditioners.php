<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirConditionerResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AirConditionerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListAirConditioners extends ListRecords
{
    protected static string $resource = AirConditionerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
