<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TableColumnPreferenceSeeder::class,
            ProductTaxonomiesSeeder::class,
            FormTabConfigurationSeeder::class,
            FormSectionConfigurationSeeder::class,
            FormFieldConfigurationSeeder::class,
        ]);
    }
}
