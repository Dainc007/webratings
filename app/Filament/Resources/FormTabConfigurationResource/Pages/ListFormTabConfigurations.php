<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormTabConfigurationResource\Pages;

use App\Filament\Resources\FormTabConfigurationResource;
use Filament\Resources\Pages\ListRecords;

final class ListFormTabConfigurations extends ListRecords
{
    protected static string $resource = FormTabConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
