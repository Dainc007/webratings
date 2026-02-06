<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormFieldConfigurationResource\Pages;

use App\Filament\Resources\FormFieldConfigurationResource;
use Filament\Resources\Pages\ListRecords;

final class ListFormFieldConfigurations extends ListRecords
{
    protected static string $resource = FormFieldConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
