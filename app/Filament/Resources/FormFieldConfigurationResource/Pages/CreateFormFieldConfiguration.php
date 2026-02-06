<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormFieldConfigurationResource\Pages;

use App\Filament\Resources\FormFieldConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateFormFieldConfiguration extends CreateRecord
{
    protected static string $resource = FormFieldConfigurationResource::class;
}
