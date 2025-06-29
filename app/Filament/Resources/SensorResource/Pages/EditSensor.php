<?php

declare(strict_types=1);

namespace App\Filament\Resources\SensorResource\Pages;

use App\Filament\Resources\SensorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditSensor extends EditRecord
{
    protected static string $resource = SensorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
