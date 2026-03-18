<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Enums\CustomFieldStatus;
use App\Filament\Resources\CustomFieldResource;
use App\Models\FormLayoutItem;
use App\Services\CustomFieldService;
use App\Services\FormLayoutService;
use Filament\Resources\Pages\CreateRecord;

final class CreateCustomField extends CreateRecord
{
    protected static string $resource = CustomFieldResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = CustomFieldStatus::PENDING;
        unset($data['target_section']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        $targetSection = $this->data['target_section'] ?? null;

        if ($targetSection) {
            $maxSort = FormLayoutItem::where('table_name', $record->table_name)
                ->where('element_type', 'field')
                ->where('parent_key', $targetSection)
                ->max('sort_order') ?? -1;

            FormLayoutItem::updateOrCreate(
                ['table_name' => $record->table_name, 'element_type' => 'field', 'element_key' => $record->column_name],
                ['parent_key' => $targetSection, 'sort_order' => $maxSort + 1],
            );

            FormLayoutService::clearCache();
        }

        CustomFieldService::createField($record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
