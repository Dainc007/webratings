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
        if (!Schema::hasColumn('air_purifiers', 'Test')) {
            Schema::table('air_purifiers', function (Blueprint $table) {
                $table->boolean('Test')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->dropColumn('Test');
        });
    }
};
