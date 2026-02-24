<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix PostgreSQL auto-increment sequences for all product tables (again).
 *
 * Sequences drift out of sync whenever records are imported with explicit IDs.
 * This is safe to run multiple times.
 */
return new class extends Migration
{
    private array $tables = [
        'air_purifiers',
        'air_humidifiers',
        'air_conditioners',
        'dehumidifiers',
        'upright_vacuums',
        'sensors',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->tables as $table) {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence(?, 'id'), COALESCE(MAX(id), 1)) FROM \"{$table}\"",
                [$table]
            );
        }
    }

    public function down(): void {}
};
