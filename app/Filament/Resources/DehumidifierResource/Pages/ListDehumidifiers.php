<?php

namespace App\Filament\Resources\DehumidifierResource\Pages;

use App\Filament\Resources\DehumidifierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDehumidifiers extends ListRecords
{
    protected static string $resource = DehumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
