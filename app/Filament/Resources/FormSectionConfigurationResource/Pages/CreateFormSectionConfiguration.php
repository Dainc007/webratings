<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormSectionConfigurationResource\Pages;

use App\Filament\Resources\FormSectionConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateFormSectionConfiguration extends CreateRecord
{
    protected static string $resource = FormSectionConfigurationResource::class;
}
