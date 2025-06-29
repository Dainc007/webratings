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
        Schema::create('shortcode_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shortcode_id')->constrained()->onDelete('cascade');
            $table->string('field');
            $table->string('operator');
            $table->string('value');
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortcode_conditions');
    }
};
