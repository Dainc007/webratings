<?php

declare(strict_types=1);

use App\Enums\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Collect all distinct brand_name values from every product table
     * and ensure each one has a corresponding record in the brands table.
     */
    public function up(): void
    {
        $brandNames = collect();

        foreach (Product::getValues() as $table) {
            $names = DB::table($table)
                ->whereNotNull('brand_name')
                ->where('brand_name', '!=', '')
                ->distinct()
                ->pluck('brand_name');

            $brandNames = $brandNames->merge($names);
        }

        $uniqueBrands = $brandNames
            ->map(fn (string $name) => mb_strtolower(trim($name)))
            ->filter()
            ->unique()
            ->values();

        $now = now();

        foreach ($uniqueBrands as $name) {
            DB::table('brands')->insertOrIgnore([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void {}
};
