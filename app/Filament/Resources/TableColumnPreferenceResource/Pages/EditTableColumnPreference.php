<?php

declare(strict_types=1);

namespace App\Filament\Resources\TableColumnPreferenceResource\Pages;

use App\Filament\Resources\TableColumnPreferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditTableColumnPreference extends EditRecord
{
    protected static string $resource = TableColumnPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
