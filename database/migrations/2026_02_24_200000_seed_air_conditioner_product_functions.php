<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $functions = [
        'urządzenie 2w1',
        'urządzenie 3w1',
        'urządzenie 4w1',
        'urządzenie 5w1',
        '2 kolory',
        'filtr hepa',
        'uszczelka na okno gratis',
        'funkcja swing',
        'praca na balkonie',
        'wi-fi',
        'filtr węglowy',
        'tryb automatyczny',
        'funkcja grzania',
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->functions as $name) {
            DB::table('product_functions')->insertOrIgnore([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('product_functions')->whereIn('name', $this->functions)->delete();
    }
};
