<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\Product;
use App\Services\TableColumnPreferenceService;

class TableColumnPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TableColumnPreferenceService::createDefaultPreferences(Product::getValues());
    }
}
