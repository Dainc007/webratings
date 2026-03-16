<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_layout_items', function (Blueprint $table) {
            $table->id();
            $table->string('table_name')->index();
            $table->string('element_type');
            $table->string('element_key');
            $table->string('parent_key')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['table_name', 'element_type', 'element_key'],
                'form_layout_items_unique'
            );

            $table->index(
                ['table_name', 'element_type', 'sort_order'],
                'form_layout_items_ordering'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_layout_items');
    }
};
