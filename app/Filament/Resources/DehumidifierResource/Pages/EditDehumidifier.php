<?php

declare(strict_types=1);

namespace App\Filament\Resources\DehumidifierResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\DehumidifierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditDehumidifier extends EditRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = DehumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
