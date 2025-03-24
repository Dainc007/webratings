<?php

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use App\Services\CustomFieldService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCustomField extends CreateRecord
{
    protected static string $resource = CustomFieldResource::class;

    protected function afterCreate(): void
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
