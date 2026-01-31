<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Fix the remote_id column to be nullable on all product tables.
 *
 * The original migrations defined remote_id as nullable, but the database
 * may have a NOT NULL constraint. This migration ensures the column is nullable.
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
        foreach ($this->tables as $table) {
            $this->makeRemoteIdNullable($table);
        }
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't reverse this migration as making columns NOT NULL
        // when they might contain NULL values would fail.
    }
};
