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
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->decimal('min_rated_power_consumption', 10, 2)->nullable()->after('max_loudness');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->dropColumn('min_rated_power_consumption');
        });
    }
};
