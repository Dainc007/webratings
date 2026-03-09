<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dehumidifiers', function (Blueprint $table) {
            $table->integer('popularity')->nullable()->after('profitability');
        });
    }

    public function down(): void
    {
        Schema::table('dehumidifiers', function (Blueprint $table) {
            $table->dropColumn('popularity');
        });
    }
};
