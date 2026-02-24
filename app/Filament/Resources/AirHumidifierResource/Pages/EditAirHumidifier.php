<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirHumidifierResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\AirHumidifierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditAirHumidifier extends EditRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = AirHumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
