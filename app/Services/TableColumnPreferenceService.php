<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TableColumnPreference;
use Illuminate\Support\Facades\Schema;

final class TableColumnPreferenceService
{
    public static function getTableColumns(string $modelClass): array
    {
        $table = $modelClass::table();

        return Schema::getColumnListing($table);
    }

    public static function createDefaultPreferences(array $tableNames): void
    {
        foreach ($tableNames as $tableName) {
            foreach (Schema::getColumnListing($tableName) as $index => $column) {
                TableColumnPreference::firstOrCreate([
                    'table_name' => $tableName,
                    'column_name' => $column,
                ], [
                    'sort_order' => $index + 1,
                    'is_visible' => false,
                ]);
            }
        }
    }
}
