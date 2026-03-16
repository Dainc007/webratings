<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('element_type');
            $table->string('element_key');
            $table->string('display_label')->nullable();
            $table->timestamps();

            $table->unique(['table_name', 'element_type', 'element_key'], 'label_overrides_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_overrides');
    }
};
