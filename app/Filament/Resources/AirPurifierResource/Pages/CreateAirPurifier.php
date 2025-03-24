<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirPurifierResource\Pages;

use App\Filament\Resources\AirPurifierResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAirPurifier extends CreateRecord
{
    protected static string $resource = AirPurifierResource::class;
}
