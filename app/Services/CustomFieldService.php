<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CustomFieldStatus;
use App\Enums\Status;
use App\Jobs\ProcessCustomFieldMigration;
use App\Models\CustomField;
use App\Models\TableColumnPreference;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\Schema;

final class CustomFieldService
{
    public static function getFormFields(string $tableName): array
    {
        $customFields = CustomField::where('table_name', $tableName)->active()->get();
        $customFieldSchema = [];
        foreach ($customFields as $customField) {
            $customFieldSchema[] = self::makeFormComponent($customField);
        }

        return $customFieldSchema;
    }

    public static function makeFormComponent(CustomField $customField): TextInput|Toggle
    {
        if ($customField->column_type === 'boolean') {
            $field = Toggle::make($customField->column_name);
        } else {
            $field = TextInput::make($customField->column_name);
        }

        if ($customField->column_type === 'integer') {
            $field->numeric();
        }

        if ($customField->display_name) {
            $field->label($customField->display_name);
        }

        return $field;
    }

    public static function getTableColumns(string $tableName): array
    {
        $availableColumns = [
            TextColumn::make('id')->hidden(),
        ];

        $columns = TableColumnPreference::where('table_name', $tableName)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($columns as $column) {
            $field = TextColumn::make($column['column_name'])
                ->wrap(false);

            if ($column['column_name'] === 'price') {
                $field = TextInputColumn::make($column['column_name'])
                    ->width('50px')
                    ->extraInputAttributes(['step' => '0.01'])
                    ->afterStateUpdated(function ($record, $state): void {
                        Notification::make()
                            ->title('Cena została zaktualizowana')
                            ->success()
                            ->send();
                    });
            }

            if ($column['column_name'] === 'brand_name') {
                $field->badge();
            }

            if ($column['column_name'] === 'status') {
                $field->badge()
                    ->formatStateUsing(function (mixed $state): string {
                        if ($state === null) {
                            return '';
                        }
                        $status = $state instanceof Status ? $state : Status::from((string) $state);

                        return $status->getLabel();
                    })
                    ->color(function (mixed $state): string {
                        if ($state === null) {
                            return 'gray';
                        }
                        $status = $state instanceof Status ? $state : Status::from((string) $state);

                        return $status->getColor();
                    })
                    ->searchable(query: function ($query, string $search): void {
                        $matchingValues = collect(Status::cases())
                            ->filter(fn (Status $s) => str_contains(
                                mb_strtolower($s->getLabel()),
                                mb_strtolower($search)
                            ))
                            ->map(fn (Status $s) => $s->value)
                            ->values()
                            ->all();

                        $query->whereIn('status', $matchingValues);
                    });
            }

            if ($column['column_name'] !== 'status') {
                $field->when(
                    Schema::hasColumn($tableName, $column['column_name']),
                    fn (): TextColumn|TextInputColumn => $field->searchable()
                );
            }

            $availableColumns[] = $field;
        }

        $customFields = CustomField::where('table_name', $tableName)->active()->get();
        foreach ($customFields as $customField) {
            if ($customField->column_type === 'boolean') {
                $field = ToggleColumn::make($customField->column_name);
            } else {
                $field = TextColumn::make($customField->column_name)
                    ->wrap(false);
            }

            if ($customField->column_type === 'integer') {
                $field->numeric();
            }

            $field->searchable();

            $field->when(
                Schema::hasColumn($tableName, $customField->column_name),
                fn (): TextColumn|ToggleColumn => $field->searchable()
            );

            $label = $customField->display_name ?? __($customField->column_name);
            $field->label($label);

            $availableColumns[] = $field;
        }

        return $availableColumns;
    }

    public static function createField(CustomField $customField): void
    {
        ProcessCustomFieldMigration::dispatch($customField, 'create');
    }

    public static function deleteField(CustomField $customField): void
    {
        $customField->update(['status' => CustomFieldStatus::DELETING]);

        ProcessCustomFieldMigration::dispatch($customField, 'delete');
    }

    public static function retryField(CustomField $customField): void
    {
        $customField->update([
            'status' => CustomFieldStatus::PENDING,
            'error_message' => null,
        ]);

        ProcessCustomFieldMigration::dispatch($customField, 'create');
    }
}
