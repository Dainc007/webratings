<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TableColumnPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

final class TableColumnPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            'air_purifiers',
        ];

        foreach ($tables as $table) {
            // Get all columns from the table
            $columns = Schema::getColumnListing($table);

            // Delete existing preferences for this table to avoid duplicates
            TableColumnPreference::where('table_name', $table)->delete();

            // Create preferences for each column with default order
            foreach ($columns as $index => $column) {
                TableColumnPreference::create([
                    'table_name' => $table,
                    'column_name' => $column,
                    'sort_order' => $index + 1,
                    'is_visible' => false,
                ]);
            }
        }
    }
}
