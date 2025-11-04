<?php

declare(strict_types=1);

namespace App\Filament\Resources\DehumidifierResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\DehumidifierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListDehumidifiers extends ListRecords
{
    protected static string $resource = DehumidifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
