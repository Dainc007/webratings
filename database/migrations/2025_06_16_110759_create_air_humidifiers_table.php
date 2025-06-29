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
        Schema::create('air_humidifiers', function (Blueprint $table) {
            $table->id();
            $table->integer('remote_id')->nullable()->unique();
            $table->string('status')->nullable();
            $table->integer('sort')->nullable();
            $table->unsignedBigInteger('user_created')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamp('date_updated')->nullable();

            // Basic product information
            $table->string('brand_name')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_before', 10, 2)->nullable();
            $table->text('discount_info')->nullable();

            // Partner information
            $table->string('partner_name')->nullable();
            $table->text('partner_link_url')->nullable();
            $table->string('partner_link_rel_2')->nullable();
            $table->string('partner_link_title')->nullable();

            // Ceneo integration
            $table->string('ceneo_link_rel_2')->nullable();
            $table->text('ceneo_url')->nullable();
            $table->string('ceneo_link_title')->nullable();

            // Media
            $table->string('image')->nullable();

            // Humidification specifications
            $table->integer('humidification_efficiency')->nullable(); // ml/h
            $table->integer('tested_efficiency')->nullable(); // ml/h
            $table->integer('max_area')->nullable(); // m²
            $table->integer('tested_max_area')->nullable(); // m²
            $table->decimal('water_tank_capacity', 8, 2)->nullable();

            $table->integer('water_tank_min_time')->nullable();
            $table->string('water_tank_fill_type')->nullable();
            $table->boolean('hygrostat')->nullable();
            $table->integer('hygrostat_min')->nullable();
            $table->integer('hygrostat_max')->nullable();
            $table->integer('hygrostat_step')->nullable();
            $table->integer('fan_modes_count')->nullable();
            $table->integer('min_fan_volume')->nullable();
            $table->integer('max_fan_volume')->nullable();
            $table->boolean('night_mode')->nullable();
            $table->boolean('evaporative_filter')->nullable();
            $table->integer('evaporative_filter_life')->nullable();
            $table->decimal('evaporative_filter_price', 10, 2)->nullable();
            $table->boolean('silver_ion')->nullable();
            $table->integer('silver_ion_life')->nullable();
            $table->decimal('silver_ion_price', 10, 2)->nullable();
            $table->boolean('ceramic_filter')->nullable();
            $table->integer('ceramic_filter_life')->nullable();
            $table->decimal('ceramic_filter_price', 10, 2)->nullable();
            $table->boolean('uv_lamp')->nullable();
            $table->boolean('ionization')->nullable();
            $table->boolean('mobile_app')->nullable();
            $table->json('mobile_features')->nullable();
            $table->json('control_other')->nullable();
            $table->boolean('remote_control')->nullable();
            $table->json('functions')->nullable();
            $table->decimal('min_rated_power_consumption', 8, 2)->nullable();
            $table->decimal('max_rated_power_consumption', 8, 2)->nullable();
            $table->string('rated_voltage')->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('depth', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('colors')->nullable();
            $table->integer('capability_points')->nullable();
            $table->integer('capability')->nullable();
            $table->integer('profitability_points')->nullable();
            $table->integer('ranking')->nullable();
            $table->integer('profitability')->nullable();
            $table->text('review_link')->nullable();
            $table->boolean('ranking_hidden')->nullable();
            $table->decimal('Filter_cots_humi', 8, 2)->nullable();
            $table->boolean('disks')->nullable();
            $table->boolean('main_ranking')->nullable();
            $table->boolean('for_plant')->nullable();
            $table->boolean('for_desk')->nullable();
            $table->boolean('alergic')->nullable();
            $table->boolean('astmatic')->nullable();
            $table->boolean('small')->nullable();
            $table->boolean('for_kids')->nullable();
            $table->boolean('big_area')->nullable();
            $table->integer('humidification_area')->nullable();
            $table->integer('max_area_ro')->nullable();
            $table->integer('max_performance')->nullable();
            $table->string('hepa_filter_class')->nullable();
            $table->boolean('mesh_filter')->nullable();
            $table->boolean('carbon_filter')->nullable();
            $table->string('type_of_device')->nullable();
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
        Schema::dropIfExists('air_humidifiers');
    }
};
