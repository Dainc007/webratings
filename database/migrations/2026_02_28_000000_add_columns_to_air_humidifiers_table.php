<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('air_humidifiers', function (Blueprint $table) {
            $table->integer('popularity')->nullable()->after('profitability');
            $table->decimal('carbon_filter_price', 10, 2)->nullable()->after('carbon_filter');
            $table->integer('carbon_filter_service_life')->nullable()->after('carbon_filter_price');
        });
    }

    public function down(): void
    {
        Schema::table('air_humidifiers', function (Blueprint $table) {
            $table->dropColumn(['popularity', 'carbon_filter_price', 'carbon_filter_service_life']);
        });
    }
};
