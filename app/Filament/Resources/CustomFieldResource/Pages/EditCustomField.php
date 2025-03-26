<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use App\Services\CustomFieldService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditCustomField extends EditRecord
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->after(function ($record): void {
                $customFieldService = app(CustomFieldService::class);

                $customFieldService->deleteField(
                    $record->table_name,
                    $record->column_name,
                );
            }),
        ];
    }
}
