<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\AirConditioner;
use App\Services\ImportBooleanCaster;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

final class AirConditionerImporter extends Importer
{
    protected static ?string $model = AirConditioner::class;

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
            ImportColumn::make('brand_name'),
            ImportColumn::make('model'),
            ImportColumn::make('type'),
            ImportColumn::make('price')
                ->numeric(),
            ImportColumn::make('price_before')
                ->numeric(),
            ImportColumn::make('discount_info'),
            ImportColumn::make('image'),
            ImportColumn::make('partner_name'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('partner_link_title'),
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('ceneo_link_title'),
            ImportColumn::make('maximum_cooling_power')
                ->numeric(),
            ImportColumn::make('max_cooling_area_manufacturer')
                ->numeric(),
            ImportColumn::make('max_cooling_area_ro')
                ->numeric(),
            ImportColumn::make('maximum_heating_power')
                ->numeric(),
            ImportColumn::make('max_heating_area_manufacturer')
                ->numeric(),
            ImportColumn::make('max_heating_area_ro')
                ->numeric(),
            ImportColumn::make('usage'),
            ImportColumn::make('colors')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('max_loudness')
                ->numeric(),
            ImportColumn::make('min_loudness')
                ->numeric(),
            ImportColumn::make('swing'),
            ImportColumn::make('max_air_flow')
                ->numeric(),
            ImportColumn::make('number_of_fan_speeds')
                ->numeric(),
            ImportColumn::make('max_performance_dry')
                ->numeric(),
            ImportColumn::make('temperature_range'),
            ImportColumn::make('max_cooling_temperature')
                ->numeric(),
            ImportColumn::make('min_cooling_temperature')
                ->numeric(),
            ImportColumn::make('min_heating_temperature')
                ->numeric(),
            ImportColumn::make('max_heating_temperature')
                ->numeric(),
            ImportColumn::make('mesh_filter')
                ->boolean(),
            ImportColumn::make('hepa_filter')
                ->boolean(),
            ImportColumn::make('hepa_filter_price')
                ->numeric(),
            ImportColumn::make('hepa_service_life')
                ->numeric(),
            ImportColumn::make('carbon_filter')
                ->boolean(),
            ImportColumn::make('carbon_filter_price')
                ->numeric(),
            ImportColumn::make('carbon_service_life')
                ->numeric(),
            ImportColumn::make('ionization')
                ->boolean(),
            ImportColumn::make('uvc')
                ->boolean(),
            ImportColumn::make('uv_light_generator'),
            ImportColumn::make('mobile_app')
                ->boolean(),
            ImportColumn::make('mobile_features')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('remote_control')
                ->boolean(),
            ImportColumn::make('product_functions_legacy')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('refrigerant_kind'),
            ImportColumn::make('needs_to_be_completed'),
            ImportColumn::make('refrigerant_amount')
                ->numeric(),
            ImportColumn::make('rated_voltage')
                ->numeric(),
            ImportColumn::make('rated_power_heating_consumption')
                ->numeric(),
            ImportColumn::make('rated_power_cooling_consumption')
                ->numeric(),
            ImportColumn::make('eer')
                ->numeric(),
            ImportColumn::make('cop')
                ->numeric(),
            ImportColumn::make('cooling_energy_class'),
            ImportColumn::make('heating_energy_class'),
            ImportColumn::make('width')
                ->numeric(),
            ImportColumn::make('height')
                ->numeric(),
            ImportColumn::make('depth')
                ->numeric(),
            ImportColumn::make('weight')
                ->numeric(),
            ImportColumn::make('manual'),
            ImportColumn::make('capability_points')
                ->numeric(),
            ImportColumn::make('capability')
                ->numeric(),
            ImportColumn::make('profitability_points')
                ->numeric(),
            ImportColumn::make('profitability')
                ->numeric(),
            ImportColumn::make('ranking')
                ->numeric(),
            ImportColumn::make('review_link'),
            ImportColumn::make('discharge_pipe')
                ->boolean(),
            ImportColumn::make('discharge_pipe_length')
                ->numeric(),
            ImportColumn::make('discharge_pipe_diameter')
                ->numeric(),
            ImportColumn::make('sealing'),
            ImportColumn::make('drain_hose')
                ->boolean(),
            ImportColumn::make('mode_cooling')
                ->boolean(),
            ImportColumn::make('mode_heating')
                ->boolean(),
            ImportColumn::make('mode_dry')
                ->boolean(),
            ImportColumn::make('mode_fan')
                ->boolean(),
            ImportColumn::make('mode_purify')
                ->boolean(),
            ImportColumn::make('max_performance_dry_condition'),
            ImportColumn::make('ranking_hidden')
                ->castStateUsing(ImportBooleanCaster::closure()),
            ImportColumn::make('functions_and_equipment_condi')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
            ImportColumn::make('small'),
            ImportColumn::make('main_ranking')
                ->castStateUsing(App\Services\ImportBooleanCaster::closure()),
            ImportColumn::make('is_promo')
                ->castStateUsing(App\Services\ImportBooleanCaster::closure())
                ->boolean(),
            ImportColumn::make('gallery')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') {
                        return null;
                    }
                    if (empty($state)) {
                        return null;
                    }

                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your air conditioner import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    public function resolveRecord(): ?AirConditioner
    {
        return AirConditioner::firstOrNew([
            'remote_id' => $this->data['remote_id'],
        ]);
    }
}
