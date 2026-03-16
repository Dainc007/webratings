<?php

declare(strict_types=1);

namespace App\Filament\Resources\LabelOverrideResource\Pages;

use App\Filament\Resources\LabelOverrideResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateLabelOverride extends CreateRecord
{
    protected static string $resource = LabelOverrideResource::class;
}
