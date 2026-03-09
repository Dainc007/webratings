<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $functions = [
            'automatycznie odszranianie',
            'programator',
            'wygaszanie podświetlania',
        ];

        $now = now();

        $records = array_map(fn (string $name) => [
            'name' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], $functions);

        DB::table('product_functions')->insertOrIgnore($records);
    }

    public function down(): void {}
};
