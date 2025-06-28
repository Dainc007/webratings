<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Sensor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

final class SensorImporter extends Importer
{
    protected static ?string $model = Sensor::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('remote_id')
                ->numeric(),
            ImportColumn::make('status'),
            ImportColumn::make('sort')
                ->numeric(),
            ImportColumn::make('user_created'),
            ImportColumn::make('date_created'),
            ImportColumn::make('user_updated'),
            ImportColumn::make('date_updated'),

            // Basic product information
            ImportColumn::make('brand_name'),
            ImportColumn::make('model'),
            ImportColumn::make('price')
                ->numeric(),
            ImportColumn::make('price_before')
                ->numeric(),
            ImportColumn::make('image'),
            ImportColumn::make('discount_info'),

            // Partner information
            ImportColumn::make('partner_name'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('partner_link_title'),

            // Ceneo integration
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('ceneo_link_title'),

            // PM1 sensor capabilities
            ImportColumn::make('is_pm1')
                ->boolean(),
            ImportColumn::make('pm1_range'),
            ImportColumn::make('pm1_accuracy'),
            ImportColumn::make('pm1_sensor_type'),

            // PM2.5 sensor capabilities
            ImportColumn::make('is_pm2')
                ->boolean(),
            ImportColumn::make('pm2_range'),
            ImportColumn::make('pm2_accuracy'),
            ImportColumn::make('pm2_sensor_type'),

            // PM10 sensor capabilities
            ImportColumn::make('is_pm10')
                ->boolean(),
            ImportColumn::make('pm10_range'),
            ImportColumn::make('pm10_accuracy'),
            ImportColumn::make('pm10_sensor_type'),

            // LZO sensor capabilities
            ImportColumn::make('is_lzo')
                ->boolean(),
            ImportColumn::make('lzo_range'),
            ImportColumn::make('lzo_accuracy'),
            ImportColumn::make('lzo_sensor_type'),

            // HCHO (Formaldehyde) sensor capabilities
            ImportColumn::make('is_hcho')
                ->boolean(),
            ImportColumn::make('hcho_range'),
            ImportColumn::make('hcho_accuracy'),
            ImportColumn::make('hcho_sensor_type'),

            // CO2 sensor capabilities
            ImportColumn::make('is_co2')
                ->boolean(),
            ImportColumn::make('co2_range'),
            ImportColumn::make('co2_accuracy'),
            ImportColumn::make('co2_sensor_type'),

            // CO sensor capabilities
            ImportColumn::make('is_co')
                ->boolean(),
            ImportColumn::make('co_range'),
            ImportColumn::make('co_accuracy'),
            ImportColumn::make('co_sensor_type'),

            // Temperature sensor capabilities
            ImportColumn::make('is_temperature')
                ->boolean(),
            ImportColumn::make('temperature_range'),
            ImportColumn::make('temperature_accuracy'),

            // Humidity sensor capabilities
            ImportColumn::make('is_humidity')
                ->boolean(),
            ImportColumn::make('humidity_range'),
            ImportColumn::make('humidity_accuracy'),

            // Pressure sensor capabilities
            ImportColumn::make('is_pressure')
                ->boolean(),
            ImportColumn::make('pressure_range'),
            ImportColumn::make('pressure_accuracy'),

            // Power and connectivity
            ImportColumn::make('battery'),
            ImportColumn::make('battery_capacity')
                ->numeric(),
            ImportColumn::make('voltage')
                ->numeric(),
            ImportColumn::make('has_power_cord')
                ->boolean(),
            ImportColumn::make('wifi')
                ->boolean(),
            ImportColumn::make('mobile_features')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('bluetooth')
                ->boolean(),

            // Device features
            ImportColumn::make('has_history')
                ->boolean(),
            ImportColumn::make('has_display')
                ->boolean(),
            ImportColumn::make('has_alarm')
                ->boolean(),
            ImportColumn::make('has_assessment')
                ->boolean(),
            ImportColumn::make('has_outdoor_indicator')
                ->boolean(),
            ImportColumn::make('has_battery_indicator')
                ->boolean(),
            ImportColumn::make('has_clock')
                ->boolean(),

            // Environmental readings
            ImportColumn::make('temperature'),
            ImportColumn::make('humidity'),

            // Physical dimensions
            ImportColumn::make('width')
                ->numeric(),
            ImportColumn::make('height')
                ->numeric(),
            ImportColumn::make('depth')
                ->numeric(),
            ImportColumn::make('weight')
                ->numeric(),

            // Capability and profitability scoring
            ImportColumn::make('capability_points')
                ->numeric(),
            ImportColumn::make('capability')
                ->numeric(),
            ImportColumn::make('profitability_points')
                ->numeric(),
            ImportColumn::make('profitability')
                ->numeric(),

            // Ranking information
            ImportColumn::make('ranking')
                ->numeric(),
            ImportColumn::make('review_link'),
            ImportColumn::make('ranking_hidden')
                ->castStateUsing(ImportBooleanCaster::closure()),
            ImportColumn::make('main_ranking')
                ->castStateUsing(ImportBooleanCaster::closure()),
        ];
    }

    public function resolveRecord(): ?Sensor
    {
        return Sensor::firstOrNew([
            'remote_id' => $this->data['remote_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your sensor import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
