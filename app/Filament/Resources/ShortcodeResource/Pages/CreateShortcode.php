<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShortcodeResource\Pages;

use App\Filament\Resources\ShortcodeResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateShortcode extends CreateRecord
{
    protected static string $resource = ShortcodeResource::class;
}
