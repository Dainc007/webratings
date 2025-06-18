<?php

namespace App\Filament\Resources\ShortcodeResource\Pages;

use App\Filament\Resources\ShortcodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShortcodes extends ListRecords
{
    protected static string $resource = ShortcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
