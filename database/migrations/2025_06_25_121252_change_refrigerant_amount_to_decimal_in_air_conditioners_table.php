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
        Schema::table('air_conditioners', function (Blueprint $table) {
            $table->decimal('refrigerant_amount', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_conditioners', function (Blueprint $table) {
            $table->integer('refrigerant_amount')->nullable()->change();
        });
    }
};
