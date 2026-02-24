<?php

declare(strict_types=1);

namespace App\Filament\Resources\UprightVacuumResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\UprightVacuumResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateUprightVacuum extends CreateRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = UprightVacuumResource::class;
}
