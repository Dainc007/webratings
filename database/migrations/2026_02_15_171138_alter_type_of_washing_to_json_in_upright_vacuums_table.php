<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert any existing non-null string values to JSON arrays
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

        Schema::table('upright_vacuums', function (Blueprint $table) {
            $table->json('type_of_washing')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upright_vacuums', function (Blueprint $table) {
            $table->string('type_of_washing')->nullable()->change();
        });

        // Convert JSON arrays back to single string values
        DB::table('upright_vacuums')
            ->whereNotNull('type_of_washing')
            ->where('type_of_washing', '!=', '')
            ->eachById(function ($record) {
                $value = json_decode($record->type_of_washing, true);

                if (is_array($value)) {
                    DB::table('upright_vacuums')
                        ->where('id', $record->id)
                        ->update(['type_of_washing' => $value[0] ?? null]);
                }
            });
    }
};
