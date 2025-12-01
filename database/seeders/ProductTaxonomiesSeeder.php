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
        // Seed Product Types from enums that end with "Type"
        foreach (DehumidifierType::cases() as $case) {
            ProductType::firstOrCreate([
                'name' => $case->getLabel(),
            ]);
        }

        // Seed Product Functions from enums that end with "Function"
        foreach (DehumidifierFunction::cases() as $case) {
            ProductFunction::firstOrCreate([
                'name' => $case->getLabel(),
            ]);
        }
    }
}
