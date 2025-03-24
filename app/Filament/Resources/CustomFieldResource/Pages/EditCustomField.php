<?php

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use App\Services\CustomFieldService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomField extends EditRecord
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->after(function ($record) {
                $customFieldService = app(CustomFieldService::class);

                $customFieldService->deleteField(
                    $record->table_name,
                    $record->column_name,
                );
            }),
        ];
    }

}
