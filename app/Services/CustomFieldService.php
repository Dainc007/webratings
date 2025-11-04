<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Status;
use App\Models\CustomField;
use App\Models\TableColumnPreference;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

final class CustomFieldService
{
    public static function getFormFields(string $tableName): array
    {
        $customFields = CustomField::where('table_name', $tableName)->get();
        $customFieldSchema = [];
        foreach ($customFields as $customField) {
            if ($customField->column_type === 'boolean') {
                $field = Toggle::make($customField->column_name);
            } else {
                $field = TextInput::make($customField->column_name);
            }

            if ($customField->column_type === 'integer') {
                $field->numeric();
            }
            $customFieldSchema[] = $field;
        }

        return $customFieldSchema;
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
            $field = TextColumn::make($column['column_name']);

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
                    ->formatStateUsing(fn (string $state): string => Status::from($state)->getLabel())
                    ->color(fn (string $state): string => Status::from($state)->getColor());
            }

            $field->when(
                Schema::hasColumn($tableName, $column['column_name']),
                fn (): TextColumn|TextInputColumn => $field->searchable()
            );

            $availableColumns[] = $field;
        }

        $customFields = CustomField::where('table_name', $tableName)->get();
        foreach ($customFields as $customField) {
            if ($customField->column_type === 'boolean') {
                $field = ToggleColumn::make($customField->column_name);
            } else {
                $field = TextColumn::make($customField->column_name);
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

    public function createField(string $tableName, string $columnName, string $columnType): true
    {
        // Generate timestamp for migration filename
        $timestamp = date('Y_m_d_His');
        $migrationName = "add_{$columnName}_to_{$tableName}_table";
        $migrationFileName = $timestamp.'_'.$migrationName.'.php';
        $migrationPath = database_path('migrations/'.$migrationFileName);

        // Read stub file
        $stubPath = resource_path('stubs/add-column.stub');
        $stub = file_get_contents($stubPath);

        // Replace placeholders
        $nullableStr = '->nullable()';
        $stub = str_replace('{{ table }}', $tableName, $stub);
        $stub = str_replace('{{ column_name }}', $columnName, $stub);
        $stub = str_replace('{{ column_type }}', $columnType, $stub);
        $stub = str_replace('{{ nullable }}', $nullableStr, $stub);

        // Create migration file
        File::put($migrationPath, $stub);

        // Run migration
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        TableColumnPreference::firstOrCreate([
            'table_name' => $tableName,
            'column_name' => $columnName,
        ], [
            'sort_order' => 0,
            'is_visible' => true,
        ]);

        return true;
    }

    public function deleteField(string $tableName, string $columnName): true
    {
        // Generate timestamp for migration filename
        $timestamp = date('Y_m_d_His');
        $migrationName = "remove_{$columnName}_from_{$tableName}_table";
        $migrationFileName = $timestamp.'_'.$migrationName.'.php';
        $migrationPath = database_path('migrations/'.$migrationFileName);

        // Read stub file
        $stubPath = resource_path('stubs/remove-column.stub');
        $stub = file_get_contents($stubPath);

        // Replace placeholders
        $stub = str_replace('{{ table }}', $tableName, $stub);
        $stub = str_replace('{{ column_name }}', $columnName, $stub);

        // Create migration file
        File::put($migrationPath, $stub);

        // Run migration
        Artisan::call('migrate', [
            '--force' => true,
        ]);

        TableColumnPreference::where(['table_name' => $tableName, 'column_name' => $columnName])->delete();

        return true;
    }
}
