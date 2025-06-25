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
        Schema::create('sensors', function (Blueprint $table) {
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
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_before', 10, 2)->nullable();
            $table->string('image')->nullable();
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

            // PM1 sensor capabilities
            $table->boolean('is_pm1')->nullable();
            $table->string('pm1_range')->nullable();
            $table->string('pm1_accuracy')->nullable();
            $table->string('pm1_sensor_type')->nullable();

            // PM2.5 sensor capabilities
            $table->boolean('is_pm2')->nullable();
            $table->string('pm2_range')->nullable();
            $table->string('pm2_accuracy')->nullable();
            $table->string('pm2_sensor_type')->nullable();

            // PM10 sensor capabilities
            $table->boolean('is_pm10')->nullable();
            $table->string('pm10_range')->nullable();
            $table->string('pm10_accuracy')->nullable();
            $table->string('pm10_sensor_type')->nullable();

            // LZO sensor capabilities
            $table->boolean('is_lzo')->nullable();
            $table->string('lzo_range')->nullable();
            $table->string('lzo_accuracy')->nullable();
            $table->string('lzo_sensor_type')->nullable();

            // HCHO (Formaldehyde) sensor capabilities
            $table->boolean('is_hcho')->nullable();
            $table->string('hcho_range')->nullable();
            $table->string('hcho_accuracy')->nullable();
            $table->string('hcho_sensor_type')->nullable();

            // CO2 sensor capabilities
            $table->boolean('is_co2')->nullable();
            $table->string('co2_range')->nullable();
            $table->string('co2_accuracy')->nullable();
            $table->string('co2_sensor_type')->nullable();

            // CO sensor capabilities
            $table->boolean('is_co')->nullable();
            $table->string('co_range')->nullable();
            $table->string('co_accuracy')->nullable();
            $table->string('co_sensor_type')->nullable();

            // Temperature sensor capabilities
            $table->boolean('is_temperature')->nullable();
            $table->string('temperature_range')->nullable();
            $table->string('temperature_accuracy')->nullable();

            // Humidity sensor capabilities
            $table->boolean('is_humidity')->nullable();
            $table->string('humidity_range')->nullable();
            $table->string('humidity_accuracy')->nullable();

            // Pressure sensor capabilities
            $table->boolean('is_pressure')->nullable();
            $table->string('pressure_range')->nullable();
            $table->string('pressure_accuracy')->nullable();

            // Power and connectivity
            $table->string('battery')->nullable();
            $table->integer('battery_capacity')->nullable();
            $table->integer('voltage')->nullable();
            $table->boolean('has_power_cord')->nullable();
            $table->boolean('wifi')->nullable();
            $table->json('mobile_features')->nullable();
            $table->boolean('bluetooth')->nullable();

            // Device features
            $table->boolean('has_history')->nullable();
            $table->boolean('has_display')->nullable();
            $table->boolean('has_alarm')->nullable();
            $table->boolean('has_assessment')->nullable();
            $table->boolean('has_outdoor_indicator')->nullable();
            $table->boolean('has_battery_indicator')->nullable();
            $table->boolean('has_clock')->nullable();

            // Environmental readings (seems to be actual measured values)
            $table->string('temperature')->nullable();
            $table->string('humidity')->nullable();

            // Physical dimensions
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('depth', 8, 2)->nullable();
            $table->decimal('weight', 8, 3)->nullable();

            // Capability and profitability scoring
            $table->integer('capability_points')->nullable();
            $table->integer('capability')->nullable();
            $table->decimal('profitability_points', 8, 2)->nullable();
            $table->integer('profitability')->nullable();

            // Ranking information
            $table->integer('ranking')->nullable();
            $table->text('review_link')->nullable();
            $table->boolean('ranking_hidden')->nullable();
            $table->string('main_ranking')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
