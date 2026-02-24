<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirConditionerResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\AirConditionerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditAirConditioner extends EditRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = AirConditionerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
