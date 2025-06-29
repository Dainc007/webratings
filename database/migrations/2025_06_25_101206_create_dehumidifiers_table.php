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
        Schema::create('dehumidifiers', function (Blueprint $table) {
            $table->id();
            $table->integer('remote_id')->nullable()->unique();
            $table->integer('sort')->nullable();
            $table->string('user_created')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->string('user_updated')->nullable();
            $table->timestamp('date_updated')->nullable();

            // Basic product information
            $table->string('brand_name')->nullable();
            $table->string('model')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_before', 10, 2)->nullable();
            $table->text('discount_info')->nullable();
            $table->string('type')->nullable();
            $table->string('image')->nullable();

            // Partner information
            $table->string('partner_name')->nullable();
            $table->text('partner_link_url')->nullable();
            $table->string('partner_link_title')->nullable();
            $table->text('ceneo_url')->nullable();
            $table->string('ceneo_link_title')->nullable();
            $table->string('status')->nullable();

            // Performance specifications
            $table->decimal('max_performance_dry', 8, 2)->nullable();
            $table->string('other_performance_condition')->nullable();
            $table->string('max_performance_dry_condition')->nullable();
            $table->integer('max_drying_area_manufacturer')->nullable();
            $table->decimal('other_performance_dry', 8, 2)->nullable();
            $table->integer('max_drying_area_ro')->nullable();

            // Physical dimensions
            $table->decimal('weight', 8, 3)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('depth', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();

            // Operating conditions
            $table->integer('minimum_temperature')->nullable();
            $table->integer('maximum_temperature')->nullable();
            $table->integer('minimum_humidity')->nullable();
            $table->integer('maximum_humidity')->nullable();

            // Power specifications
            $table->integer('rated_power_consumption')->nullable();
            $table->integer('rated_voltage')->nullable();
            $table->string('refrigerant_kind')->nullable();
            $table->decimal('refrigerant_amount', 8, 2)->nullable();
            $table->string('needs_to_be_completed')->nullable();

            // Features and functions
            $table->json('functions')->nullable();
            $table->decimal('water_tank_capacity', 8, 2)->nullable();
            $table->decimal('minimum_fill_time', 8, 2)->nullable();
            $table->decimal('average_filling_time', 8, 2)->nullable();

            // Hygrostat settings
            $table->json('higrostat')->nullable();
            $table->integer('min_value_for_hygrostat')->nullable();
            $table->integer('max_value_for_hygrostat')->nullable();
            $table->string('increment_of_the_hygrostat')->nullable();

            // Air flow specifications
            $table->integer('number_of_fan_speeds')->nullable();
            $table->integer('max_air_flow')->nullable();
            $table->integer('max_loudness')->nullable();
            $table->integer('min_loudness')->nullable();
            $table->json('modes_of_operation')->nullable();

            // Filters
            $table->boolean('mesh_filter')->nullable();
            $table->integer('hepa_service_life')->nullable();
            $table->decimal('hepa_filter_price', 10, 2)->nullable();
            $table->boolean('hepa_filter')->nullable();
            $table->boolean('carbon_filter')->nullable();
            $table->integer('carbon_service_life')->nullable();
            $table->decimal('carbon_filter_price', 10, 2)->nullable();

            // Additional technologies
            $table->boolean('ionization')->nullable();
            $table->boolean('uvc')->nullable();
            $table->json('uv_light_generator')->nullable();

            // Connectivity and control
            $table->boolean('mobile_app')->nullable();
            $table->json('mobile_features')->nullable();
            $table->json('partner_link_rel_2')->nullable();
            $table->json('ceneo_link_rel_2')->nullable();

            // Documentation
            $table->string('manual_file')->nullable();

            // Scoring and ranking
            $table->integer('capability_points')->nullable();
            $table->integer('capability')->nullable();
            $table->decimal('profitability_points', 8, 2)->nullable();
            $table->integer('profitability')->nullable();
            $table->integer('ranking')->nullable();
            $table->text('review_link')->nullable();
            $table->boolean('ranking_hidden')->nullable();

            // Additional equipment and features
            $table->json('functions_and_equipment_dehumi')->nullable();
            $table->string('main_ranking')->nullable();
            $table->boolean('is_promo')->nullable();
            $table->json('gallery')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dehumidifiers');
    }
};
