<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add missing `colors` JSON column to the dehumidifiers table.
 *
 * The model has 'colors' => 'array' cast and the form has a TagsInput for colors,
 * but the original migration never created this column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dehumidifiers', function (Blueprint $table) {
            if (! Schema::hasColumn('dehumidifiers', 'colors')) {
                $table->json('colors')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('dehumidifiers', function (Blueprint $table) {
            $table->dropColumn('colors');
        });
    }
};
