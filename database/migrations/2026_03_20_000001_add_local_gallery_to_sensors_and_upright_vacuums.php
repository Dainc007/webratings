<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensors', function (Blueprint $blueprint) {
            $blueprint->json('local_gallery')->nullable()->after('local_image');
        });

        Schema::table('upright_vacuums', function (Blueprint $blueprint) {
            $blueprint->json('local_gallery')->nullable()->after('local_image');
        });
    }

    public function down(): void
    {
        Schema::table('sensors', function (Blueprint $blueprint) {
            $blueprint->dropColumn('local_gallery');
        });

        Schema::table('upright_vacuums', function (Blueprint $blueprint) {
            $blueprint->dropColumn('local_gallery');
        });
    }
};
