<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_column_preferences', function (Blueprint $table) {
            $table->id();

            $table->string('table_name');
            $table->string('column_name');
            $table->integer('sort_order');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['table_name', 'column_name', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_column_preferences');
    }
};
