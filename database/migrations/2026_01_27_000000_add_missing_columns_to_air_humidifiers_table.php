<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('air_humidifiers', function (Blueprint $table) {
            // Timer fields - used in the form but missing from schema
            $table->boolean('timer')->nullable()->default(false);
            $table->integer('timer_min')->nullable();
            $table->integer('timer_max')->nullable();

            // Fan volume toggle - the min/max fields exist but toggle is missing
            $table->boolean('fan_volume')->nullable()->default(false);

            // Auto mode fields
            $table->boolean('auto_mode')->nullable()->default(false);
            $table->integer('auto_mode_min')->nullable();
            $table->integer('auto_mode_max')->nullable();

            // Night mode min/max - toggle exists but range fields missing
            $table->integer('night_mode_min')->nullable();
            $table->integer('night_mode_max')->nullable();

            // Child lock fields
            $table->boolean('child_lock')->nullable()->default(false);
            $table->integer('child_lock_min')->nullable();
            $table->integer('child_lock_max')->nullable();

            // Display fields
            $table->boolean('display')->nullable()->default(false);
            $table->integer('display_min')->nullable();
            $table->integer('display_max')->nullable();

            // Remote control min/max - toggle exists but range fields missing
            $table->integer('remote_control_min')->nullable();
            $table->integer('remote_control_max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_humidifiers', function (Blueprint $table) {
            $table->dropColumn([
                'timer',
                'timer_min',
                'timer_max',
                'fan_volume',
                'auto_mode',
                'auto_mode_min',
                'auto_mode_max',
                'night_mode_min',
                'night_mode_max',
                'child_lock',
                'child_lock_min',
                'child_lock_max',
                'display',
                'display_min',
                'display_max',
                'remote_control_min',
                'remote_control_max',
            ]);
        });
    }
};
