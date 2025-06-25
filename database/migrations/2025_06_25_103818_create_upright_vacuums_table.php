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
        Schema::create('upright_vacuums', function (Blueprint $table) {
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
            $table->json('colors')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('price_date')->nullable();
            $table->decimal('price_before', 10, 2)->nullable();
            $table->text('discount_info')->nullable();

            // Partner information
            $table->string('partner_name')->nullable();
            $table->text('partner_link_url')->nullable();
            $table->json('partner_link_rel_2')->nullable();
            $table->string('partner_link_title')->nullable();

            // Ceneo integration
            $table->text('ceneo_url')->nullable();
            $table->json('ceneo_link_rel_2')->nullable();
            $table->string('ceneo_link_title')->nullable();

            // Vacuum type and power specifications
            $table->json('vacuum_cleaner_type')->nullable();
            $table->integer('suction_power_aw')->nullable();
            $table->integer('suction_power_pa')->nullable();
            $table->integer('number_of_suction_power_levels')->nullable();
            $table->string('automatic_power_adjustment')->nullable();
            $table->integer('suction_power_highest_level_pa')->nullable();
            $table->integer('suction_power_medium_level_pa')->nullable();
            $table->integer('suction_power_low_level_pa')->nullable();
            $table->integer('maximum_engine_power')->nullable();
            $table->integer('rotation_speed')->nullable();
            $table->integer('noise_level')->nullable();

            // Power and battery specifications
            $table->string('battery_change')->nullable();
            $table->decimal('cable_length', 8, 2)->nullable();
            $table->json('power_supply')->nullable();
            $table->string('maximum_operation_time')->nullable();
            $table->string('battery_charging_time')->nullable();
            $table->decimal('battery_voltage', 8, 2)->nullable();
            $table->integer('battery_capacity')->nullable();

            // Cleaning functions
            $table->string('mopping_function')->nullable();
            $table->string('active_washing_function')->nullable();
            $table->string('self_cleaning_function')->nullable();
            $table->string('self_cleaning_underlays')->nullable();
            $table->decimal('clean_water_tank_capacity', 8, 3)->nullable();
            $table->decimal('dirty_water_tank_capacity', 8, 3)->nullable();
            $table->decimal('dust_tank_capacity', 8, 3)->nullable();

            // Device features
            $table->string('hand_vacuum_cleaner')->nullable();
            $table->string('led_backlight')->nullable();
            $table->string('uv_technology')->nullable();
            $table->string('detecting_dirt_on_the_floor')->nullable();
            $table->string('detecting_carpet')->nullable();
            $table->string('display')->nullable();
            $table->json('display_type')->nullable();

            // Filtration system
            $table->string('pollution_filtration_system')->nullable();
            $table->string('cyclone_technology')->nullable();
            $table->string('mesh_filter')->nullable();
            $table->string('hepa_filter')->nullable();
            $table->string('epa_filter')->nullable();

            // Brushes and attachments
            $table->string('electric_brush')->nullable();
            $table->string('bendable_pipe')->nullable();
            $table->string('turbo_brush')->nullable();
            $table->string('carpet_and_floor_brush')->nullable();
            $table->string('attachment_for_pets')->nullable();
            $table->string('telescopic_tube')->nullable();
            $table->string('charging_station')->nullable();
            $table->json('additional_equipment')->nullable();

            // Suitability
            $table->string('for_pet_owners')->nullable();
            $table->string('for_allergy_sufferers')->nullable();

            // Physical specifications
            $table->decimal('weight', 8, 3)->nullable();
            $table->integer('warranty')->nullable();

            // Performance scoring
            $table->integer('profitability')->nullable();
            $table->integer('capability')->nullable();
            $table->decimal('capability_points', 8, 2)->nullable();
            $table->decimal('profitability_points', 8, 2)->nullable();
            $table->integer('ranking')->nullable();
            $table->text('review_link')->nullable();

            // Additional time specifications
            $table->string('mopping_time_max')->nullable();
            $table->string('vacuuming_time_max')->nullable();
            $table->string('easy_emptying_tank')->nullable();
            $table->string('continuous_work')->nullable();
            $table->string('displaying_battery_status')->nullable();
            $table->string('operation_time_turbo')->nullable();
            $table->string('operation_time_eco')->nullable();
            $table->decimal('weight_hand', 8, 3)->nullable();
            $table->string('type_of_washing')->nullable();

            // Ranking and categorization
            $table->string('main_ranking')->nullable();
            $table->string('type')->nullable();
            $table->boolean('ranking_hidden')->nullable();
            $table->boolean('is_promo')->nullable();
            $table->string('videorecenzja1')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upright_vacuums');
    }
};
