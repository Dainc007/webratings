<?php

declare(strict_types=1);

namespace App\Filament\Resources\SensorResource\Pages;

use App\Filament\Resources\SensorResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSensor extends CreateRecord
{
    protected static string $resource = SensorResource::class;
}
