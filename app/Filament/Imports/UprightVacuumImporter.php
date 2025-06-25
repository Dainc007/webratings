<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\UprightVacuum;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

final class UprightVacuumImporter extends Importer
{
    protected static ?string $model = UprightVacuum::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('remote_id')
                ->label('id')
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
            ImportColumn::make('colors')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('image'),
            ImportColumn::make('price')
                ->numeric(),
            ImportColumn::make('price_date'),
            ImportColumn::make('price_before')
                ->numeric(),
            ImportColumn::make('discount_info'),
            ImportColumn::make('partner_name'),
            ImportColumn::make('partner_link_url'),
            ImportColumn::make('partner_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('partner_link_title'),
            ImportColumn::make('ceneo_url'),
            ImportColumn::make('ceneo_link_rel_2')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('ceneo_link_title'),
            ImportColumn::make('vacuum_cleaner_type')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('suction_power_aw')
                ->numeric(),
            ImportColumn::make('suction_power_pa')
                ->numeric(),
            ImportColumn::make('number_of_suction_power_levels')
                ->numeric(),
            ImportColumn::make('automatic_power_adjustment'),
            ImportColumn::make('suction_power_highest_level_pa')
                ->numeric(),
            ImportColumn::make('suction_power_medium_level_pa')
                ->numeric(),
            ImportColumn::make('suction_power_low_level_pa')
                ->numeric(),
            ImportColumn::make('maximum_engine_power')
                ->numeric(),
            ImportColumn::make('rotation_speed')
                ->numeric(),
            ImportColumn::make('noise_level')
                ->numeric(),
            ImportColumn::make('battery_change'),
            ImportColumn::make('cable_length')
                ->numeric(),
            ImportColumn::make('power_supply')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('maximum_operation_time'),
            ImportColumn::make('battery_charging_time'),
            ImportColumn::make('battery_voltage')
                ->numeric(),
            ImportColumn::make('battery_capacity')
                ->numeric(),
            ImportColumn::make('mopping_function'),
            ImportColumn::make('active_washing_function'),
            ImportColumn::make('self_cleaning_function'),
            ImportColumn::make('self_cleaning_underlays'),
            ImportColumn::make('clean_water_tank_capacity')
                ->numeric(),
            ImportColumn::make('dirty_water_tank_capacity')
                ->numeric(),
            ImportColumn::make('dust_tank_capacity')
                ->numeric(),
            ImportColumn::make('hand_vacuum_cleaner'),
            ImportColumn::make('led_backlight'),
            ImportColumn::make('uv_technology'),
            ImportColumn::make('detecting_dirt_on_the_floor'),
            ImportColumn::make('detecting_carpet'),
            ImportColumn::make('display'),
            ImportColumn::make('display_type')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('pollution_filtration_system'),
            ImportColumn::make('cyclone_technology'),
            ImportColumn::make('mesh_filter'),
            ImportColumn::make('hepa_filter'),
            ImportColumn::make('epa_filter'),
            ImportColumn::make('electric_brush'),
            ImportColumn::make('bendable_pipe'),
            ImportColumn::make('turbo_brush'),
            ImportColumn::make('carpet_and_floor_brush'),
            ImportColumn::make('attachment_for_pets'),
            ImportColumn::make('telescopic_tube'),
            ImportColumn::make('charging_station'),
            ImportColumn::make('additional_equipment')
                ->castStateUsing(function ($state) {
                    if (is_null($state) || $state === '' || $state === 'null') return null;
                    if (empty($state)) return null;
                    return json_encode(array_filter(array_map('trim', explode('","', trim($state, '[]"')))));
                }),
            ImportColumn::make('for_pet_owners'),
            ImportColumn::make('for_allergy_sufferers'),
            ImportColumn::make('weight')
                ->numeric(),
            ImportColumn::make('warranty')
                ->numeric(),
            ImportColumn::make('profitability')
                ->numeric(),
            ImportColumn::make('capability')
                ->numeric(),
            ImportColumn::make('capability_points')
                ->numeric(),
            ImportColumn::make('profitability_points')
                ->numeric(),
            ImportColumn::make('ranking')
                ->numeric(),
            ImportColumn::make('review_link'),
            ImportColumn::make('mopping_time_max'),
            ImportColumn::make('vacuuming_time_max'),
            ImportColumn::make('easy_emptying_tank'),
            ImportColumn::make('continuous_work'),
            ImportColumn::make('displaying_battery_status'),
            ImportColumn::make('operation_time_turbo'),
            ImportColumn::make('operation_time_eco'),
            ImportColumn::make('weight_hand')
                ->numeric(),
            ImportColumn::make('type_of_washing'),
            ImportColumn::make('main_ranking'),
            ImportColumn::make('type'),
            ImportColumn::make('ranking_hidden')
                ->castStateUsing(function ($state) {
                    if (is_bool($state)) return $state;
                    if (is_null($state) || $state === '') return false;
                    $trueValues = ['1', 1, 'true', 'yes', 'tak', 'y', 't', true];
                    $falseValues = ['0', 0, 'false', 'no', 'nie', 'n', 'f', false];
                    $stateLower = is_string($state) ? strtolower(trim($state)) : $state;
                    if (in_array($stateLower, $trueValues, true)) return true;
                    if (in_array($stateLower, $falseValues, true)) return false;
                    return false;
                }),
            ImportColumn::make('is_promo')
                ->castStateUsing(function ($state) {
                    if (is_bool($state)) return $state;
                    if (is_null($state) || $state === '') return false;
                    $trueValues = ['1', 1, 'true', 'yes', 'tak', 'y', 't', true];
                    $falseValues = ['0', 0, 'false', 'no', 'nie', 'n', 'f', false];
                    $stateLower = is_string($state) ? strtolower(trim($state)) : $state;
                    if (in_array($stateLower, $trueValues, true)) return true;
                    if (in_array($stateLower, $falseValues, true)) return false;
                    return false;
                }),
            ImportColumn::make('videorecenzja1'),
        ];
    }

    public function resolveRecord(): ?UprightVacuum
    {
        return new UprightVacuum();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your upright vacuum import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
