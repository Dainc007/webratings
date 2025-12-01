<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShortcodeResource\Pages;

use App\Filament\Resources\ShortcodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditShortcode extends EditRecord
{
    protected static string $resource = ShortcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
