<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $functions = [
            'tryb auto', 'jonizator plazmowy', 'sterowanie smartfonem',
            'pilot', '4 kolory', '3 lata gwarancji',
            'higrostat', 'timer', 'lampka nocna', 'aromaterapia',
            'lampa uv', 'automatyczne wył. przy pustym zbiorniku',
            'optyczny wskaźnik napełnienia zbiornika',
            'sygnalizacja pustego zbiornika', 'funkcja osuszania filtra',
            'blokada rodzicielska', 'sygnalizacja zużycia filtra',
            'kółka jezdne', 'wskaźnik poziomu nawilżenia',
            'zakres prezentowanej wilgotności (w %)',
            'programator czasu pracy (wyłączenia)',
            'wskaźnik temperatury', 'brak funkcji dodatkowych',
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
