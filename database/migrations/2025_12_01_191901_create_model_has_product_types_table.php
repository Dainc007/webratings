<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_has_product_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')
                ->constrained('product_types')
                ->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->timestamps();

            $table->index(['model_type', 'model_id'], 'model_has_product_types_model_index');
            $table->unique(['product_type_id', 'model_type', 'model_id'], 'model_has_product_types_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_product_types');
    }
};
