<?php

declare(strict_types=1);

namespace App\Filament\Resources\DehumidifierResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\DehumidifierResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateDehumidifier extends CreateRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = DehumidifierResource::class;
}
