<?php

namespace App\Filament\Imports;

use App\Models\Sensor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SensorImporter extends Importer
{
    protected static ?string $model = Sensor::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('remote_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('status'),
            ImportColumn::make('sort')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('user_created'),
            ImportColumn::make('date_created')
                ->rules(['datetime']),
            ImportColumn::make('user_updated'),
            ImportColumn::make('date_updated')
                ->rules(['datetime']),
            ImportColumn::make('brand_name'),
            ImportColumn::make('model'),
            ImportColumn::make('price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('price_before')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('image'),
            ImportColumn::make('discount_info'),
            ImportColumn::make('partner_name'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_rel_2'),
            ImportColumn::make('partner_link_title'),
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_rel_2'),
            ImportColumn::make('ceneo_link_title'),
            ImportColumn::make('is_pm1')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('pm1_range'),
            ImportColumn::make('pm1_accuracy'),
            ImportColumn::make('pm1_sensor_type'),
            ImportColumn::make('is_pm2')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('pm2_range'),
            ImportColumn::make('pm2_accuracy'),
            ImportColumn::make('pm2_sensor_type'),
            ImportColumn::make('is_pm10')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('pm10_range'),
            ImportColumn::make('pm10_accuracy'),
            ImportColumn::make('pm10_sensor_type'),
            ImportColumn::make('is_lzo')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('lzo_range'),
            ImportColumn::make('lzo_accuracy'),
            ImportColumn::make('lzo_sensor_type'),
            ImportColumn::make('is_hcho')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('hcho_range'),
            ImportColumn::make('hcho_accuracy'),
            ImportColumn::make('hcho_sensor_type'),
            ImportColumn::make('is_co2')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('co2_range'),
            ImportColumn::make('co2_accuracy'),
            ImportColumn::make('co2_sensor_type'),
            ImportColumn::make('is_co')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('co_range'),
            ImportColumn::make('co_accuracy'),
            ImportColumn::make('co_sensor_type'),
            ImportColumn::make('is_temperature')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('temperature_range'),
            ImportColumn::make('temperature_accuracy'),
            ImportColumn::make('is_humidity')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('humidity_range'),
            ImportColumn::make('humidity_accuracy'),
            ImportColumn::make('is_pressure')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('pressure_range'),
            ImportColumn::make('pressure_accuracy'),
            ImportColumn::make('battery'),
            ImportColumn::make('battery_capacity')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('voltage')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('has_power_cord')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('wifi')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('mobile_features'),
            ImportColumn::make('bluetooth')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_history')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_display')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_alarm')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_assessment')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_outdoor_indicator')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_battery_indicator')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('has_clock')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('temperature'),
            ImportColumn::make('humidity'),
            ImportColumn::make('width')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('height')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('depth')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('weight')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('capability_points')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('capability')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('profitability_points')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('profitability')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ranking')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('review_link'),
            ImportColumn::make('ranking_hidden')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('main_ranking'),
        ];
    }

    public function resolveRecord(): ?Sensor
    {
        // return Sensor::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Sensor();
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
