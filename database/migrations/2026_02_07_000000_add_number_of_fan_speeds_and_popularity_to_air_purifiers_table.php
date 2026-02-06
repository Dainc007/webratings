<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->integer('number_of_fan_speeds')->nullable()->after('max_area_ro');
            $table->integer('popularity')->nullable()->after('profitability_points');
        });
    }

    public function down(): void
    {
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->dropColumn(['number_of_fan_speeds', 'popularity']);
        });
    }
};
