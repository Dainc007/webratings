<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirHumidifierResource\Pages;

use App\Filament\Resources\AirHumidifierResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAirHumidifier extends CreateRecord
{
    protected static string $resource = AirHumidifierResource::class;
}
