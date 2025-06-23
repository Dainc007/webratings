<?php

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
            $table->decimal('min_rated_power_consumption', 8, 2)->nullable()->change();
            $table->decimal('max_rated_power_consumption', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_humidifiers', function (Blueprint $table) {
            $table->integer('min_rated_power_consumption')->nullable()->change();
            $table->integer('max_rated_power_consumption')->nullable()->change();
        });
    }
};
