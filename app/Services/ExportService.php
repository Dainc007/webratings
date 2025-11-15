<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CustomField;
use App\Models\TableColumnPreference;
use Illuminate\Support\Facades\Schema;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

final class ExportService
{
    /**
     * Get all database columns for a table (including hidden ones)
     */
    public static function getAllColumnsForTable(string $tableName): array
    {
        return Schema::getColumnListing($tableName);
    }

    /**
     * Create export columns from table columns with custom labels
     */
    public static function createExportColumns(string $tableName): array
    {
        $columns = self::getAllColumnsForTable($tableName);
        
        return collect($columns)
            ->map(function (string $columnName) use ($tableName) {
                $column = Column::make($columnName);
                
                // Try to get label from CustomField
                $customField = CustomField::where('table_name', $tableName)
                    ->where('column_name', $columnName)
                    ->first();
                
                if ($customField && $customField->display_name) {
                    $column->heading($customField->display_name);
                } else {
                    // Fallback to formatted column name
                    $column->heading(self::formatColumnLabel($columnName));
                }
                
                return $column;
            })
            ->toArray();
    }

    /**
     * Get export configuration for ExportAction (header action - exports all records)
     */
    public static function getExportAllActionConfig(string $tableName, ?string $customFilename = null): array
    {
        $filename = $customFilename ?? $tableName . '_export_' . date('Y-m-d_His');
        
        return [
            ExcelExport::make('all_records')
                ->withFilename($filename)
                ->withColumns(self::createExportColumns($tableName))
                ->modifyQueryUsing(fn ($query) => $query) // Export all records
        ];
    }

    /**
     * Get export configuration for ExportBulkAction (bulk action - exports selected records)
     */
    public static function getExportBulkActionConfig(string $tableName, ?string $customFilename = null): array
    {
        $filename = $customFilename ?? $tableName . '_selected_' . date('Y-m-d_His');
        
        return [
            ExcelExport::make('selected_records')
                ->withFilename(fn ($livewire, $recordIds) => $filename . '_' . count($recordIds) . '_records')
                ->withColumns(self::createExportColumns($tableName))
                ->modifyQueryUsing(fn ($query, $recordIds) => $query->whereIn('id', $recordIds))
        ];
    }

    /**
     * Get a formatted label for a column name
     */
    public static function formatColumnLabel(string $columnName): string
    {
        return str($columnName)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }
}

