<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirConditionerResource\Pages;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\AirConditionerResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAirConditioner extends CreateRecord
{
    use HandlesRecordExceptions;

    protected static string $resource = AirConditionerResource::class;
}
