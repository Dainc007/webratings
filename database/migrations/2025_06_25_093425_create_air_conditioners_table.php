<?php

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
        Schema::create('air_conditioners', function (Blueprint $table) {
            $table->id();
            $table->integer('remote_id')->nullable()->unique();
            $table->string('status')->nullable();
            $table->integer('sort')->nullable();
            $table->string('user_created')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->string('user_updated')->nullable();
            $table->timestamp('date_updated')->nullable();

            // Basic product information
            $table->string('brand_name')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_before', 10, 2)->nullable();
            $table->text('discount_info')->nullable();
            $table->string('image')->nullable();

            // Partner information
            $table->string('partner_name')->nullable();
            $table->text('partner_link_url')->nullable();
            $table->json('partner_link_rel_2')->nullable();
            $table->string('partner_link_title')->nullable();

            // Ceneo integration
            $table->text('ceneo_url')->nullable();
            $table->json('ceneo_link_rel_2')->nullable();
            $table->string('ceneo_link_title')->nullable();

            // Cooling capabilities
            $table->decimal('maximum_cooling_power', 8, 2)->nullable();
            $table->integer('max_cooling_area_manufacturer')->nullable();
            $table->integer('max_cooling_area_ro')->nullable();

            // Heating capabilities
            $table->decimal('maximum_heating_power', 8, 2)->nullable();
            $table->integer('max_heating_area_manufacturer')->nullable();
            $table->integer('max_heating_area_ro')->nullable();

            // Installation and usage
            $table->string('usage')->nullable();
            $table->json('colors')->nullable();

            // Performance specifications
            $table->integer('max_loudness')->nullable();
            $table->integer('min_loudness')->nullable();
            $table->string('swing')->nullable();
            $table->integer('max_air_flow')->nullable();
            $table->integer('number_of_fan_speeds')->nullable();
            $table->decimal('max_performance_dry', 8, 2)->nullable();

            // Temperature control
            $table->string('temperature_range')->nullable();
            $table->integer('max_cooling_temperature')->nullable();
            $table->integer('min_cooling_temperature')->nullable();
            $table->integer('min_heating_temperature')->nullable();
            $table->integer('max_heating_temperature')->nullable();

            // Filtration system
            $table->boolean('mesh_filter')->nullable();
            $table->boolean('hepa_filter')->nullable();
            $table->decimal('hepa_filter_price', 8, 2)->nullable();
            $table->integer('hepa_service_life')->nullable();
            $table->boolean('carbon_filter')->nullable();
            $table->decimal('carbon_filter_price', 8, 2)->nullable();
            $table->integer('carbon_service_life')->nullable();

            // Air purification features
            $table->boolean('ionization')->nullable();
            $table->boolean('uvc')->nullable();
            $table->json('uv_light_generator')->nullable();

            // Smart features and connectivity
            $table->boolean('mobile_app')->nullable();
            $table->json('mobile_features')->nullable();
            $table->boolean('remote_control')->nullable();
            $table->json('functions')->nullable();

            // Refrigerant system
            $table->string('refrigerant_kind')->nullable();
            $table->string('needs_to_be_completed')->nullable();
            $table->decimal('refrigerant_amount', 8, 2)->nullable();

            // Power consumption and efficiency
            $table->integer('rated_voltage')->nullable();
            $table->integer('rated_power_heating_consumption')->nullable();
            $table->integer('rated_power_cooling_consumption')->nullable();
            $table->decimal('eer', 8, 2)->nullable();
            $table->decimal('cop', 8, 2)->nullable();
            $table->string('cooling_energy_class')->nullable();
            $table->string('heating_energy_class')->nullable();

            // Physical dimensions
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('depth', 8, 2)->nullable();
            $table->decimal('weight', 8, 3)->nullable();

            // Documentation and scoring
            $table->string('manual')->nullable();
            $table->integer('capability_points')->nullable();
            $table->integer('capability')->nullable();
            $table->decimal('profitability_points', 8, 2)->nullable();
            $table->integer('profitability')->nullable();

            // Ranking information
            $table->integer('ranking')->nullable();
            $table->text('review_link')->nullable();

            // Installation accessories
            $table->boolean('discharge_pipe')->nullable();
            $table->integer('discharge_pipe_length')->nullable();
            $table->decimal('discharge_pipe_diameter', 8, 2)->nullable();
            $table->string('sealing')->nullable();
            $table->boolean('drain_hose')->nullable();

            // Operating modes
            $table->boolean('mode_cooling')->nullable();
            $table->boolean('mode_heating')->nullable();
            $table->boolean('mode_dry')->nullable();
            $table->boolean('mode_fan')->nullable();
            $table->boolean('mode_purify')->nullable();

            // Additional specifications
            $table->string('max_performance_dry_condition')->nullable();
            $table->boolean('ranking_hidden')->nullable();
            $table->json('functions_and_equipment_condi')->nullable();
            $table->string('small')->nullable();
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
        Schema::dropIfExists('air_conditioners');
    }
};
