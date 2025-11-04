<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShortcodeResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ShortcodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListShortcodes extends ListRecords
{
    protected static string $resource = ShortcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
