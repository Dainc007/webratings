<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Change type_of_washing from varchar to json on upright_vacuums.
 *
 * Production data contains numeric string values ('1', '2', '3')
 * which are first wrapped in JSON arrays ('["1"]', '["2"]', '["3"]')
 * before the column type is changed.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Convert existing scalar values to JSON arrays
        DB::table('upright_vacuums')
            ->whereNotNull('type_of_washing')
            ->where('type_of_washing', '!=', '')
            ->eachById(function ($record) {
                $value = $record->type_of_washing;

                // Skip if already a valid JSON array
                if (is_string($value) && str_starts_with($value, '[')) {
                    return;
                }

                // Wrap the scalar string value in a JSON array
                DB::table('upright_vacuums')
                    ->where('id', $record->id)
                    ->update(['type_of_washing' => json_encode([$value])]);
            });

        // Step 2: Change column type from varchar to json/jsonb
        // Use raw SQL with USING clause for PostgreSQL (required for varchar -> jsonb cast).
        // Fall back to Schema builder for SQLite/other drivers.
        try {
            DB::statement('ALTER TABLE upright_vacuums ALTER COLUMN type_of_washing TYPE jsonb USING type_of_washing::jsonb');
        } catch (\Throwable) {
            Schema::table('upright_vacuums', function (Blueprint $table) {
                $table->json('type_of_washing')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE upright_vacuums ALTER COLUMN type_of_washing TYPE varchar(255) USING type_of_washing::text');
        } catch (\Throwable) {
            Schema::table('upright_vacuums', function (Blueprint $table) {
                $table->string('type_of_washing')->nullable()->change();
            });
        }

        // Convert JSON arrays back to single string values
        DB::table('upright_vacuums')
            ->whereNotNull('type_of_washing')
            ->where('type_of_washing', '!=', '')
            ->eachById(function ($record) {
                $value = $record->type_of_washing;

                // Handle both string (SQLite) and already-decoded (PostgreSQL) values
                if (is_string($value)) {
                    $value = json_decode($value, true);
                }

                if (is_array($value)) {
                    DB::table('upright_vacuums')
                        ->where('id', $record->id)
                        ->update(['type_of_washing' => $value[0] ?? null]);
                }
            });
    }
};
