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
    }
}
