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
        $humidifierTypes = [
            'Oczyszczacz powietrza z nawilżaczem',
            'Nawilżacz powietrza z oczyszczaczem',
        ];

        foreach ($humidifierTypes as $typeName) {
            ProductType::firstOrCreate([
                'name' => $typeName,
            ]);
        }
    }
}
