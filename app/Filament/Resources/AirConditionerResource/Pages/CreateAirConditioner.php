<?php

declare(strict_types=1);

namespace App\Filament\Resources\AirConditionerResource\Pages;

use App\Filament\Resources\AirConditionerResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateAirConditioner extends CreateRecord
{
    protected static string $resource = AirConditionerResource::class;
}
