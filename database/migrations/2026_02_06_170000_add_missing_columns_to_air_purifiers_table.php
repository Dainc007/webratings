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
            $table->json('gallery')->nullable()->after('review_link');
            $table->integer('hygrostat_min')->nullable()->after('hygrostat');
            $table->integer('hygrostat_max')->nullable()->after('hygrostat_min');
        });
    }

    public function down(): void
    {
        Schema::table('air_purifiers', function (Blueprint $table) {
            $table->dropColumn(['gallery', 'hygrostat_min', 'hygrostat_max']);
        });
    }
};
