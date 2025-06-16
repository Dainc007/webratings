<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\AirHumidifier;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

final class AirHumidifierImporter extends Importer
{
    protected static ?string $model = AirHumidifier::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('remote_id'),
            ImportColumn::make('status'),
            ImportColumn::make('date_created'),
            ImportColumn::make('date_updated'),
            ImportColumn::make('model'),
            ImportColumn::make('brand_name'),
            ImportColumn::make('price'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_rel_2')
                ->castStateUsing(function ($state) {
                    return json_encode(array_map('trim', explode(',', $state)));
                }),
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_rel_2')
                ->castStateUsing(function ($state) {
                    return json_encode(array_map('trim', explode(',', $state)));
                }),
            ImportColumn::make('max_performance'),
            ImportColumn::make('max_area'),
            ImportColumn::make('max_area_ro'),
            ImportColumn::make('humidification_efficiency'),
            ImportColumn::make('tested_efficiency'),
            ImportColumn::make('water_tank_capacity'),
            ImportColumn::make('water_tank_min_time'),
            ImportColumn::make('water_tank_fill_type'),
            ImportColumn::make('hygrostat')
                ->boolean(),
            ImportColumn::make('hygrostat_min'),
            ImportColumn::make('hygrostat_max'),
            ImportColumn::make('hygrostat_step'),
            ImportColumn::make('fan_modes_count'),
            ImportColumn::make('min_fan_volume'),
            ImportColumn::make('max_fan_volume'),
            ImportColumn::make('night_mode')
                ->boolean(),
            ImportColumn::make('evaporative_filter')
                ->boolean(),
            ImportColumn::make('evaporative_filter_life'),
            ImportColumn::make('evaporative_filter_price'),
            ImportColumn::make('silver_ion')
                ->boolean(),
            ImportColumn::make('silver_ion_life'),
            ImportColumn::make('silver_ion_price'),
            ImportColumn::make('ceramic_filter')
                ->boolean(),
            ImportColumn::make('ceramic_filter_life'),
            ImportColumn::make('ceramic_filter_price'),
            ImportColumn::make('uv_lamp')
                ->boolean(),
            ImportColumn::make('ionization')
                ->boolean(),
            ImportColumn::make('mobile_app')
                ->boolean(),
            ImportColumn::make('mobile_features'),
            ImportColumn::make('control_other'),
            ImportColumn::make('remote_control')
                ->boolean(),
            ImportColumn::make('functions'),
            ImportColumn::make('min_rated_power_consumption'),
            ImportColumn::make('max_rated_power_consumption'),
            ImportColumn::make('rated_voltage'),
            ImportColumn::make('width'),
            ImportColumn::make('height'),
            ImportColumn::make('weight'),
            ImportColumn::make('depth'),
            ImportColumn::make('review_link'),
            ImportColumn::make('colors'),
            ImportColumn::make('capability_points'),
            ImportColumn::make('capability'),
            ImportColumn::make('profitability_points'),
            ImportColumn::make('ranking'),
            ImportColumn::make('profitability'),
            ImportColumn::make('ranking_hidden')
                ->boolean(),
            ImportColumn::make('Filter_cots_humi'),
            ImportColumn::make('disks')
                ->boolean(),
            ImportColumn::make('main_ranking'),
            ImportColumn::make('for_plant')
                ->boolean(),
            ImportColumn::make('for_desk')
                ->boolean(),
            ImportColumn::make('alergic')
                ->boolean(),
            ImportColumn::make('astmatic')
                ->boolean(),
            ImportColumn::make('small')
                ->boolean(),
            ImportColumn::make('for_kids')
                ->boolean(),
            ImportColumn::make('big_area')
                ->boolean(),
            ImportColumn::make('humidification_area'),
            ImportColumn::make('max_area_ro'),
            ImportColumn::make('max_performance'),
            ImportColumn::make('hepa_filter_class'),
            ImportColumn::make('mesh_filter')
                ->boolean(),
            ImportColumn::make('carbon_filter')
                ->boolean(),
            ImportColumn::make('type_of_device'),
            ImportColumn::make('is_promo')
                ->boolean(),
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
