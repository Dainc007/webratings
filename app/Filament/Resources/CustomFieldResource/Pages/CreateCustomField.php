<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use App\Services\CustomFieldService;
use Filament\Resources\Pages\CreateRecord;

final class CreateCustomField extends CreateRecord
{
    protected static string $resource = CustomFieldResource::class;

    private function afterCreate(): void
    {
        $record = $this->record;
        $customFieldService = app(CustomFieldService::class);

        $customFieldService->createField(
            $record->table_name,
            $record->column_name,
            $record->column_type,
        );
    }
}
