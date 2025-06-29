<?php

declare(strict_types=1);

namespace App\Filament\Resources\UprightVacuumResource\Pages;

use App\Filament\Resources\UprightVacuumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditUprightVacuum extends EditRecord
{
    protected static string $resource = UprightVacuumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
