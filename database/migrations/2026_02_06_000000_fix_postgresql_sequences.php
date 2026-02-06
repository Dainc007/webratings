<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix PostgreSQL auto-increment sequences for all product tables.
 *
 * When records are imported with explicit IDs, the PostgreSQL sequence
 * doesn't advance automatically. This causes UniqueConstraintViolationException
 * when creating new records via the Filament admin panel.
 *
 * @see https://www.postgresql.org/docs/current/functions-sequence.html
 */
return new class extends Migration
{
    /**
     * Tables that need sequence resets.
     */
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
            $maxId = DB::table($table)->max('id') ?? 0;

            if ($maxId > 0) {
                $sequenceName = "{$table}_id_seq";
                DB::statement("SELECT setval('{$sequenceName}', ?)", [$maxId]);
            }
        }
    }

    public function down(): void
    {
        // Sequences don't need to be reverted - they're always correct after this migration
    }
};
