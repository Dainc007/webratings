<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomFieldResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CustomFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListCustomFields extends ListRecords
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
