<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DehumidifierFunction;
use App\Enums\DehumidifierType;
use App\Models\ProductFunction;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

final class ProductTaxonomiesSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Product Types from DehumidifierType enum
        foreach (DehumidifierType::cases() as $case) {
            ProductType::firstOrCreate([
                'name' => $case->getLabel(),
            ]);
        }

        // Seed Product Functions from DehumidifierFunction enum
        foreach (DehumidifierFunction::cases() as $case) {
            ProductFunction::firstOrCreate([
                'name' => $case->getLabel(),
            ]);
        }

        // Seed humidifier-specific product types
        // Values must be lowercase to match the ProductType name mutator (mb_strtolower),
        // otherwise firstOrCreate won't find existing records on PostgreSQL (case-sensitive).
        $humidifierTypes = [
            'oczyszczacz powietrza z nawilżaczem',
            'nawilżacz powietrza z oczyszczaczem',
        ];

        foreach ($humidifierTypes as $typeName) {
            ProductType::firstOrCreate([
                'name' => $typeName,
            ]);
        }

        // Seed air conditioner product functions
        // Values lowercase to match ProductFunction name mutator (mb_strtolower)
        $acFunctions = [
            'urządzenie 2w1', 'urządzenie 3w1', 'urządzenie 4w1', 'urządzenie 5w1',
            '2 kolory', 'filtr hepa', 'uszczelka na okno gratis', 'funkcja swing',
            'praca na balkonie', 'wi-fi', 'filtr węglowy', 'tryb automatyczny',
            'funkcja grzania',
        ];

        foreach ($acFunctions as $name) {
            ProductFunction::firstOrCreate([
                'name' => $name,
            ]);
        }

        $apFunctions = [
            'tryb auto', 'jonizator plazmowy', 'sterowanie smartfonem',
            'pilot', '4 kolory', '3 lata gwarancji',
        ];

        foreach ($apFunctions as $name) {
            ProductFunction::firstOrCreate([
                'name' => $name,
            ]);
        }

        $humidifierFunctions = [
            'higrostat', 'timer', 'lampka nocna', 'aromaterapia',
            'sterowanie smartfonem', 'lampa uv',
            'automatyczne wył. przy pustym zbiorniku',
            'optyczny wskaźnik napełnienia zbiornika',
            'sygnalizacja pustego zbiornika', 'funkcja osuszania filtra',
            'blokada rodzicielska', 'sygnalizacja zużycia filtra',
            'kółka jezdne', 'wskaźnik poziomu nawilżenia',
            'zakres prezentowanej wilgotności (w %)',
            'programator czasu pracy (wyłączenia)',
            'wskaźnik temperatury', 'brak funkcji dodatkowych',
        ];

        foreach ($humidifierFunctions as $name) {
            ProductFunction::firstOrCreate([
                'name' => $name,
            ]);
        }
    }
}
