<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix the remote_id column to be nullable on all product tables.
 *
 * The original migrations defined remote_id as nullable, but the database
 * may have a NOT NULL constraint. This migration ensures the column is nullable.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix air_purifiers table
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->integer('remote_id')->nullable()->unique()->change();
        });

        // Fix air_humidifiers table
        Schema::table('air_humidifiers', function (Blueprint $table) {
            $table->integer('remote_id')->nullable()->unique()->change();
        });

        // Fix air_conditioners table
        Schema::table('air_conditioners', function (Blueprint $table) {
            $table->integer('remote_id')->nullable()->unique()->change();
        });

        // Fix dehumidifiers table
        Schema::table('dehumidifiers', function (Blueprint $table) {
            $table->integer('remote_id')->nullable()->unique()->change();
        });

        // Fix upright_vacuums table
        Schema::table('upright_vacuums', function (Blueprint $table) {
            $table->integer('remote_id')->nullable()->unique()->change();
        });

        // Fix sensors table
        Schema::table('sensors', function (Blueprint $table) {
            $table->integer('remote_id')->nullable()->unique()->change();
        });
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
