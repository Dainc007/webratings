<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rename legacy `productFunctions` JSON columns to `product_functions_legacy`.
 *
 * These tables originally stored product functions as a JSON column named
 * `productFunctions`. The application has since migrated to a polymorphic
 * many-to-many relationship (model_has_product_functions pivot table).
 *
 * The camelCase column name conflicts with the Eloquent relationship method
 * `productFunctions()`, causing Filament's Select component to throw
 * "The relationship [productFunctions] does not exist on the model" on edit pages,
 * because `hasAttribute('productFunctions')` returns true and shadows the relationship.
 */
return new class extends Migration
{
    private array $tables = [
        'air_purifiers',
        'air_humidifiers',
        'air_conditioners',
        'dehumidifiers',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'productFunctions')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('productFunctions', 'product_functions_legacy');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'product_functions_legacy')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->renameColumn('product_functions_legacy', 'productFunctions');
                });
            }
        }
    }
};
