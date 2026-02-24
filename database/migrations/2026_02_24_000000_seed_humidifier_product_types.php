<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $types = [
        'oczyszczacz powietrza z nawilżaczem',
        'nawilżacz powietrza z oczyszczaczem',
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->types as $name) {
            DB::table('product_types')->insertOrIgnore([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('product_types')->whereIn('name', $this->types)->delete();
    }
};
