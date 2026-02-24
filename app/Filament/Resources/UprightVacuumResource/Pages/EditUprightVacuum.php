<?php

declare(strict_types=1);

namespace App\Filament\Resources\UprightVacuumResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\UprightVacuumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditUprightVacuum extends EditRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = UprightVacuumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
