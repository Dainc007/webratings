<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TableColumnPreference;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

final class CustomFieldService
{
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
