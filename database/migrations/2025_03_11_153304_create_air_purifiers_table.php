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
        Schema::create('air_purifiers', function (Blueprint $table) {
            $table->id();
            $table->integer('remote_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->timestamp('date_updated')->nullable();
            $table->string('model')->nullable();
            $table->string('brand_name')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('partner_link_url')->nullable();
            $table->string('partner_link_rel_2')->nullable();
            $table->string('ceneo_url')->nullable();
            $table->string('ceneo_link_rel_2')->nullable();
            $table->decimal('max_performance', 10, 2)->nullable();
            $table->decimal('max_area', 10, 2)->nullable();
            $table->decimal('max_area_ro', 10, 2)->nullable();
            $table->boolean('has_humidification')->default(false);
            $table->string('humidification_type')->nullable();
            $table->boolean('humidification_switch')->default(false)->nullable();
            $table->decimal('humidification_efficiency', 10, 2)->nullable();
            $table->decimal('humidification_area', 10, 2)->nullable();
            $table->decimal('water_tank_capacity', 10, 2)->nullable();
            $table->boolean('hygrometer')->default(false)->nullable();
            $table->boolean('hygrostat')->default(false)->nullable();
            $table->string('evaporative_filter')->nullable();
            $table->string('evaporative_filter_life')->nullable();
            $table->decimal('evaporative_filter_price', 10, 2)->nullable();
            $table->string('ionizer_type')->nullable();
            $table->boolean('ionizer_switch')->default(false)->nullable();
            $table->boolean('mesh_filter')->default(false)->nullable();
            $table->boolean('hepa_filter')->default(false)->nullable();
            $table->string('hepa_filter_service_life')->nullable();
            $table->decimal('hepa_filter_price', 10, 2)->nullable();
            $table->boolean('carbon_filter')->default(false)->nullable();
            $table->string('carbon_filter_service_life')->nullable();
            $table->decimal('carbon_filter_price', 10, 2)->nullable();
            $table->boolean('uvc')->default(false)->nullable();
            $table->boolean('mobile_app')->default(false)->nullable();
            $table->boolean('remote_control')->default(false)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('depth', 10, 2)->nullable();
            $table->string('review_link')->nullable();
            $table->boolean('ionization')->default(false)->nullable();
            $table->string('capability_points')->nullable();
            $table->string('profitability_points')->nullable();
            $table->decimal('min_loudness', 10, 2)->nullable();
            $table->decimal('max_loudness', 10, 2)->nullable();
            $table->decimal('max_rated_power_consumption', 10, 2)->nullable();
            $table->string('certificates')->nullable();
            $table->boolean('pm2_sensor')->default(false)->nullable();
            $table->string('colors')->nullable();
            $table->string('functions')->nullable();
            $table->boolean('lzo_tvcop_sensor')->default(false)->nullable();
            $table->boolean('temperature_sensor')->default(false)->nullable();
            $table->boolean('humidity_sensor')->default(false)->nullable();
            $table->boolean('light_sensor')->default(false)->nullable();
            $table->string('hepa_filter_class')->nullable();
            $table->decimal('effectiveness_hepa_filter', 10, 2)->nullable();

            $table->date('price_date')->nullable();
            $table->string('ranking_hidden')->nullable();

            $table->string('filter_costs')->nullable();
            $table->string('functions_and_equipment')->nullable();
            $table->boolean('heating_and_cooling_function')->default(false)->nullable();

            $table->string('main_ranking')->nullable();
            $table->boolean('for_kids')->default(false)->nullable();
            $table->boolean('cooling_function')->default(false)->nullable();
            $table->boolean('bedroom')->default(false)->nullable();
            $table->boolean('smokers')->default(false)->nullable();
            $table->boolean('office')->default(false)->nullable();
            $table->boolean('kindergarten')->default(false)->nullable();
            $table->boolean('astmatic')->default(false)->nullable();
            $table->boolean('alergic')->default(false)->nullable();
            $table->string('type_of_device')->nullable()->nullable();
            $table->boolean('type')->default(false)->nullable();
            $table->boolean('is_promo')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('air_purifiers');
    }
};
