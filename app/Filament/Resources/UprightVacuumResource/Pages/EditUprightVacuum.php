<?php

namespace App\Filament\Resources\UprightVacuumResource\Pages;

use App\Filament\Resources\UprightVacuumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUprightVacuum extends EditRecord
{
    protected static string $resource = UprightVacuumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
