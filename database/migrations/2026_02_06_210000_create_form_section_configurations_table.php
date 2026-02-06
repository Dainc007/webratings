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
        Schema::create('form_section_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('tab_key');
            $table->string('section_key');
            $table->string('section_label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('columns')->default(1);
            $table->boolean('is_collapsible')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->string('depends_on')->nullable();
            $table->timestamps();

            $table->unique(['table_name', 'tab_key', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_section_configurations');
    }
};
