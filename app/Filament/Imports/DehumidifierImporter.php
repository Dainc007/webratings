<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Dehumidifier;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

final class DehumidifierImporter extends Importer
{
    protected static ?string $model = Dehumidifier::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('remote_id')
                ->label('id')
                ->numeric(),
            ImportColumn::make('sort')
                ->numeric(),
            ImportColumn::make('user_created'),
            ImportColumn::make('date_created'),
            ImportColumn::make('user_updated'),
            ImportColumn::make('date_updated'),
            ImportColumn::make('brand_name'),
            ImportColumn::make('model'),
            ImportColumn::make('price')
                ->numeric(),
            ImportColumn::make('price_before')
                ->numeric(),
            ImportColumn::make('discount_info'),
            ImportColumn::make('type'),
            ImportColumn::make('image'),
            ImportColumn::make('partner_name'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_title'),
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_title'),
            ImportColumn::make('status'),
            ImportColumn::make('max_performance_dry')
                ->numeric(),
            ImportColumn::make('other_performance_condition'),
            ImportColumn::make('max_performance_dry_condition'),
            ImportColumn::make('max_drying_area_manufacturer')
                ->numeric(),
            ImportColumn::make('other_performance_dry')
                ->numeric(),
            ImportColumn::make('max_drying_area_ro')
                ->numeric(),
            ImportColumn::make('weight')
                ->numeric(),
            ImportColumn::make('width')
                ->numeric(),
            ImportColumn::make('depth')
                ->numeric(),
            ImportColumn::make('height')
                ->numeric(),
            ImportColumn::make('minimum_temperature')
                ->numeric(),
            ImportColumn::make('maximum_temperature')
                ->numeric(),
            ImportColumn::make('minimum_humidity')
                ->numeric(),
            ImportColumn::make('maximum_humidity')
                ->numeric(),
            ImportColumn::make('rated_power_consumption')
                ->numeric(),
            ImportColumn::make('rated_voltage')
                ->numeric(),
            ImportColumn::make('refrigerant_kind'),
            ImportColumn::make('refrigerant_amount')
                ->numeric(),
            ImportColumn::make('needs_to_be_completed'),
            ImportColumn::make('functions')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('water_tank_capacity')
                ->numeric(),
            ImportColumn::make('minimum_fill_time')
                ->numeric(),
            ImportColumn::make('average_filling_time')
                ->numeric(),
            ImportColumn::make('higrostat')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('min_value_for_hygrostat')
                ->numeric(),
            ImportColumn::make('max_value_for_hygrostat')
                ->numeric(),
            ImportColumn::make('increment_of_the_hygrostat')
                ->numeric(),
            ImportColumn::make('number_of_fan_speeds')
                ->numeric(),
            ImportColumn::make('max_air_flow')
                ->numeric(),
            ImportColumn::make('max_loudness')
                ->numeric(),
            ImportColumn::make('min_loudness')
                ->numeric(),
            ImportColumn::make('modes_of_operation')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('mesh_filter')
                ->boolean(),
            ImportColumn::make('hepa_service_life')
                ->numeric(),
            ImportColumn::make('hepa_filter_price')
                ->numeric(),
            ImportColumn::make('hepa_filter')
                ->boolean(),
            ImportColumn::make('carbon_filter')
                ->boolean(),
            ImportColumn::make('carbon_service_life')
                ->numeric(),
            ImportColumn::make('carbon_filter_price')
                ->numeric(),
            ImportColumn::make('ionization')
                ->boolean(),
            ImportColumn::make('uvc')
                ->boolean(),
            ImportColumn::make('uv_light_generator')
                ->boolean(),
            ImportColumn::make('mobile_app')
                ->boolean(),
            ImportColumn::make('mobile_features')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode(' ', trim($state, '[]"')))));
                }),
            ImportColumn::make('partner_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('ceneo_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('manual_file'),
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
            ImportColumn::make('ranking_hidden')
                ->boolean(),
            ImportColumn::make('functions_and_equipment_dehumi')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('main_ranking')
            ->castStateUsing(App\Services\ImportBooleanCaster::closure()),

            ImportColumn::make('is_promo')
            ->castStateUsing(App\Services\ImportBooleanCaster::closure())
                ->boolean(),
            ImportColumn::make('gallery')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode(',', $state))));
                }),
        ];
    }

    public function resolveRecord(): ?Dehumidifier
    {
        return Dehumidifier::firstOrNew([
            'remote_id' => $this->data['remote_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your dehumidifier import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
