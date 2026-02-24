<?php

declare(strict_types=1);

use App\Models\ProductType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $types = [
            'Oczyszczacz powietrza z nawilżaczem',
            'Nawilżacz powietrza z oczyszczaczem',
        ];

        foreach ($types as $typeName) {
            ProductType::firstOrCreate([
                'name' => $typeName,
            ]);
        }
    }

    public function down(): void
    {
        ProductType::whereIn('name', [
            'oczyszczacz powietrza z nawilżaczem',
            'nawilżacz powietrza z oczyszczaczem',
        ])->delete();
    }
};
