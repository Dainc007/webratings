<?php

namespace App\Filament\Imports;

use App\Models\AirHumidifier;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AirHumidifierImporter extends Importer
{
    protected static ?string $model = AirHumidifier::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('remote_id'),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('sort')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('user_created')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('date_created'),
            ImportColumn::make('user_updated')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('date_updated'),
            ImportColumn::make('brand_name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('model')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('type'),
            ImportColumn::make('price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('price_before')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('discount_info'),
            ImportColumn::make('partner_name'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_rel_2'),
            ImportColumn::make('partner_link_title'),
            ImportColumn::make('ceneo_link_rel_2'),
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_title'),
            ImportColumn::make('image'),
            ImportColumn::make('humidification_efficiency')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('tested_efficiency')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('max_area')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('tested_max_area')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('water_tank_capacity')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('water_tank_min_time')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('water_tank_fill_type'),
            ImportColumn::make('hygrostat')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('hygrostat_min')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('hygrostat_max')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('hygrostat_step')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('fan_modes_count')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('min_fan_volume')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('max_fan_volume')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('night_mode')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('evaporative_filter')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('evaporative_filter_life')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('evaporative_filter_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('silver_ion')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('silver_ion_life')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('silver_ion_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ceramic_filter')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('ceramic_filter_life')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ceramic_filter_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('uv_lamp')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('ionization')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('mobile_app')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('mobile_features'),
            ImportColumn::make('control_other'),
            ImportColumn::make('remote_control')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('functions'),
            ImportColumn::make('min_rated_power_consumption')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('max_rated_power_consumption')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('rated_voltage'),
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
            ImportColumn::make('colors'),
            ImportColumn::make('capability_points')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('capability')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('profitability_points')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ranking')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('profitability')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('review_link'),
            ImportColumn::make('ranking_hidden')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('Filter_cots_humi')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('disks')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('main_ranking')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('for_plant')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('for_desk')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('alergic')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('astmatic')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('small')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('for_kids')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('big_area')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('humidification_area')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('max_area_ro')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('max_performance')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('hepa_filter_class'),
            ImportColumn::make('mesh_filter')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('carbon_filter')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('type_of_device'),
            ImportColumn::make('is_promo')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('gallery'),
        ];
    }

    public function resolveRecord(): ?AirHumidifier
    {
        return AirHumidifier::firstOrNew([
            'id' => $this->data['remote_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your air humidifier import has completed and ' . number_format($import->successful_rows) . ' ' . str(
                'row'
            )->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural(
                    $failedRowsCount
                ) . ' failed to import.';
        }

        return $body;
    }
}
