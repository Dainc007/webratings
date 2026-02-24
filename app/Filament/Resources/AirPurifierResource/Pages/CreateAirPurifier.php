<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirPurifierResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\AirPurifierResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAirPurifier extends CreateRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = AirPurifierResource::class;
}
