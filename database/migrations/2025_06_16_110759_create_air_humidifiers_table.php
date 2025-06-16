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
        Schema::create('air_humidifiers', function (Blueprint $table) {
            $table->id();
            $table->integer('remote_id')->nullable()->unique();;
            $table->string('status')->default('active');
            $table->integer('sort')->default(0);
            $table->unsignedBigInteger('user_created')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamp('date_updated')->nullable();

            // Basic product information
            $table->string('brand_name');
            $table->string('model');
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

            // Water tank specifications
            $table->decimal('water_tank_capacity', 5, 2)->nullable(); // liters
            $table->integer('water_tank_min_time')->nullable(); // hours
            $table->string('water_tank_fill_type')->nullable();

            // Hygrostat settings
            $table->boolean('hygrostat')->default(false);
            $table->integer('hygrostat_min')->nullable(); // %
            $table->integer('hygrostat_max')->nullable(); // %
            $table->integer('hygrostat_step')->nullable(); // %

            // Fan specifications
            $table->integer('fan_modes_count')->nullable();
            $table->integer('min_fan_volume')->nullable(); // dB
            $table->integer('max_fan_volume')->nullable(); // dB
            $table->boolean('night_mode')->default(false);

            // Evaporative filter
            $table->boolean('evaporative_filter')->default(false);
            $table->integer('evaporative_filter_life')->nullable(); // months
            $table->decimal('evaporative_filter_price', 8, 2)->nullable();

            // Silver ion
            $table->boolean('silver_ion')->default(false);
            $table->integer('silver_ion_life')->nullable(); // months
            $table->decimal('silver_ion_price', 8, 2)->nullable();

            // Ceramic filter
            $table->boolean('ceramic_filter')->default(false);
            $table->integer('ceramic_filter_life')->nullable(); // months
            $table->decimal('ceramic_filter_price', 8, 2)->nullable();

            // Additional features
            $table->boolean('uv_lamp')->default(false);
            $table->boolean('ionization')->default(false);

            // Control features
            $table->boolean('mobile_app')->default(false);
            $table->text('mobile_features')->nullable();
            $table->text('control_other')->nullable();
            $table->boolean('remote_control')->default(false);
            $table->text('functions')->nullable();

            // Power specifications
            $table->integer('min_rated_power_consumption')->nullable(); // watts
            $table->integer('max_rated_power_consumption')->nullable(); // watts
            $table->string('rated_voltage')->nullable(); // e.g., "230V"

            // Physical dimensions
            $table->decimal('width', 8, 2)->nullable(); // cm
            $table->decimal('height', 8, 2)->nullable(); // cm
            $table->decimal('depth', 8, 2)->nullable(); // cm
            $table->decimal('weight', 8, 2)->nullable(); // kg
            $table->string('colors')->nullable();

            // Ranking and scoring
            $table->integer('capability_points')->nullable();
            $table->integer('capability')->nullable();
            $table->integer('profitability_points')->nullable();
            $table->integer('ranking')->nullable();
            $table->integer('profitability')->nullable();
            $table->text('review_link')->nullable();
            $table->boolean('ranking_hidden')->default(false);

            // Additional specifications
            $table->decimal('Filter_cots_humi', 8, 2)->nullable();
            $table->boolean('disks')->default(false);
            $table->integer('main_ranking')->nullable();

            // Usage categories (boolean flags)
            $table->boolean('for_plant')->default(false);
            $table->boolean('for_desk')->default(false);
            $table->boolean('alergic')->default(false);
            $table->boolean('astmatic')->default(false);
            $table->boolean('small')->default(false);
            $table->boolean('for_kids')->default(false);
            $table->boolean('big_area')->default(false);

            // Additional area specifications
            $table->integer('humidification_area')->nullable(); // m²
            $table->integer('max_area_ro')->nullable(); // m²
            $table->integer('max_performance')->nullable();

            // Filter specifications
            $table->string('hepa_filter_class')->nullable();
            $table->boolean('mesh_filter')->default(false);
            $table->boolean('carbon_filter')->default(false);
            $table->string('type_of_device')->nullable();

            // Promotional and media
            $table->boolean('is_promo')->default(false);
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
