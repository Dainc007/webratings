<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES_WITH_IMAGE = [
        'air_conditioners',
        'air_humidifiers',
        'dehumidifiers',
        'sensors',
        'upright_vacuums',
    ];

    private const TABLES_WITH_GALLERY = [
        'air_conditioners',
        'air_humidifiers',
        'air_purifiers',
        'dehumidifiers',
    ];

    public function up(): void
    {
        foreach (self::TABLES_WITH_IMAGE as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->string('local_image')->nullable()->after('image');
            });
        }

        foreach (self::TABLES_WITH_GALLERY as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->json('local_gallery')->nullable()->after('gallery');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES_WITH_IMAGE as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('local_image');
            });
        }

        foreach (self::TABLES_WITH_GALLERY as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('local_gallery');
            });
        }
    }
};
