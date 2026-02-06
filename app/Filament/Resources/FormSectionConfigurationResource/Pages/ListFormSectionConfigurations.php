<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormSectionConfigurationResource\Pages;

use App\Filament\Resources\FormSectionConfigurationResource;
use Filament\Resources\Pages\ListRecords;

final class ListFormSectionConfigurations extends ListRecords
{
    protected static string $resource = FormSectionConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
