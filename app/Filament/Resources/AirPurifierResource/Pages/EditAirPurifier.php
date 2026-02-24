<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirPurifierResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\AirPurifierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditAirPurifier extends EditRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = AirPurifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
