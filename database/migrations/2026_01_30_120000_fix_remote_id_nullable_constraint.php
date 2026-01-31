<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Fix database schema issues:
 * 1. Make remote_id nullable on all product tables
 * 2. Fix air_purifiers.colors column type from string to jsonb
 * 3. Fix air_purifiers.functions_and_equipment column type from string to jsonb
 *
 * Using raw SQL because Laravel's ->change() with ->unique() tries to recreate
 * the constraint which already exists.
 */
return new class extends Migration
{
    /**
     * Tables that need the remote_id column fixed.
     */
    private array $tables = [
        'air_purifiers',
        'air_humidifiers',
        'air_conditioners',
        'dehumidifiers',
        'upright_vacuums',
        'sensors',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix remote_id nullable constraint
        foreach ($this->tables as $table) {
            $this->makeRemoteIdNullable($table);
        }

        // Fix air_purifiers column types (string -> jsonb for array fields)
        $this->fixAirPurifiersColumnTypes();
    }

    /**
     * Make the remote_id column nullable for a given table.
     * Silently handles cases where the column is already nullable.
     */
    private function makeRemoteIdNullable(string $table): void
    {
        try {
            // Check if the column exists and is NOT NULL
            $columnInfo = DB::selectOne("
                SELECT is_nullable 
                FROM information_schema.columns 
                WHERE table_schema = 'public' 
                  AND table_name = ? 
                  AND column_name = 'remote_id'
            ", [$table]);

            if ($columnInfo && $columnInfo->is_nullable === 'NO') {
                DB::statement("ALTER TABLE {$table} ALTER COLUMN remote_id DROP NOT NULL");
                Log::info("Made remote_id nullable on {$table}");
            }
        } catch (\Exception $e) {
            Log::warning("Could not modify remote_id on {$table}: " . $e->getMessage());
        }
    }

    /**
     * Fix air_purifiers column types that should be JSON but are string.
     */
    private function fixAirPurifiersColumnTypes(): void
    {
        $columnsToFix = ['colors', 'functions_and_equipment', 'type_of_device'];

        foreach ($columnsToFix as $column) {
            try {
                // Check if column exists and is not already jsonb
                $columnInfo = DB::selectOne("
                    SELECT data_type 
                    FROM information_schema.columns 
                    WHERE table_schema = 'public' 
                      AND table_name = 'air_purifiers' 
                      AND column_name = ?
                ", [$column]);

                if ($columnInfo && $columnInfo->data_type !== 'jsonb') {
                    // Convert existing data to JSON format and change column type
                    // First, update any non-null values to be valid JSON arrays
                    DB::statement("
                        UPDATE air_purifiers 
                        SET {$column} = CASE 
                            WHEN {$column} IS NULL OR {$column} = '' THEN '[]'::text
                            WHEN {$column} LIKE '[%' THEN {$column}
                            ELSE jsonb_build_array({$column})::text
                        END
                        WHERE {$column} IS NOT NULL
                    ");

                    // Set empty strings to empty JSON arrays
                    DB::statement("UPDATE air_purifiers SET {$column} = '[]' WHERE {$column} = '' OR {$column} IS NULL");

                    // Change column type to jsonb
                    DB::statement("ALTER TABLE air_purifiers ALTER COLUMN {$column} TYPE jsonb USING {$column}::jsonb");

                    Log::info("Fixed air_purifiers.{$column} column type to jsonb");
                }
            } catch (\Exception $e) {
                Log::warning("Could not fix air_purifiers.{$column}: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't reverse this migration as:
        // 1. Making columns NOT NULL when they might contain NULL values would fail
        // 2. Converting jsonb back to string would lose data structure
    }
};
