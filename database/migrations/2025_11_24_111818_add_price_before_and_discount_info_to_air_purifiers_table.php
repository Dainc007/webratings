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
            $table->decimal('price_before', 10, 2)->nullable()->after('price');
            $table->text('discount_info')->nullable()->after('price_before');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->dropColumn(['price_before', 'discount_info']);
        });
    }
};
