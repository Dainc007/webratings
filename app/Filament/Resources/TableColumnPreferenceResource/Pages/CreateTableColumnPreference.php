<?php

declare(strict_types=1);

namespace App\Filament\Resources\TableColumnPreferenceResource\Pages;

use App\Filament\Resources\TableColumnPreferenceResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTableColumnPreference extends CreateRecord
{
    protected static string $resource = TableColumnPreferenceResource::class;
}
