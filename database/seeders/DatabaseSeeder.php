<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Product;
use App\Services\TableColumnPreferenceService;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TableColumnPreferenceService::createDefaultPreferences(Product::getValues());
    }
}
