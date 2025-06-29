<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirConditionerResource\Pages;

use App\Filament\Resources\AirConditionerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditAirConditioner extends EditRecord
{
    protected static string $resource = AirConditionerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
