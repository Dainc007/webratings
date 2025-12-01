<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\AirPurifier;
use App\Services\ImportBooleanCaster;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

final class AirPurifierImporter extends Importer
{
    protected static ?string $model = AirPurifier::class;

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
            ImportColumn::make('partner_link_rel_2')->castStateUsing(function ($state) {
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
            ImportColumn::make('has_humidification')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('humidification_type'),
            ImportColumn::make('humidification_switch')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('humidification_efficiency'),
            ImportColumn::make('humidification_area')
                ->numeric(),
            ImportColumn::make('water_tank_capacity')
                ->numeric(),
            ImportColumn::make('hygrometer')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('hygrostat')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('evaporative_filter'),
            ImportColumn::make('evaporative_filter_life')
                ->numeric(),
            ImportColumn::make('evaporative_filter_price')
                ->numeric(),
            ImportColumn::make('ionizer_type'),
            ImportColumn::make('ionizer_switch')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('mesh_filter')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('hepa_filter')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('hepa_filter_service_life')
                ->numeric(),
            ImportColumn::make('hepa_filter_price')
                ->numeric(),
            ImportColumn::make('carbon_filter')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('carbon_filter_service_life')
                ->numeric(),
            ImportColumn::make('carbon_filter_price')
                ->numeric(),
            ImportColumn::make('uvc')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('mobile_app')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('remote_control')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('width')
                ->numeric(),
            ImportColumn::make('height')
                ->numeric(),
            ImportColumn::make('weight')
                ->numeric(),
            ImportColumn::make('depth')
                ->numeric(),
            ImportColumn::make('review_link'),
            ImportColumn::make('ionization')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('capability_points')
                ->numeric(),
            ImportColumn::make('profitability_points')
                ->numeric(),
            ImportColumn::make('min_loudness')
                ->numeric(),
            ImportColumn::make('max_loudness')
                ->numeric(),
            ImportColumn::make('max_rated_power_consumption')
                ->numeric(),
            ImportColumn::make('certificates'),
            ImportColumn::make('pm2_sensor')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('colors')
                ->castStateUsing(function ($state): array {
                    if (empty($state)) {
                        return [];
                    }
                    // If it's already a JSON string, decode it first
                    if (is_string($state) && str_starts_with(mb_trim($state), '[')) {
                        $state = json_decode($state, true);
                    }
                    // If it's a string, split by comma
                    if (is_string($state)) {
                        return array_map('trim', explode(',', $state));
                    }
                    // If it's already an array, return it
                    if (is_array($state)) {
                        return array_map('trim', $state);
                    }

                    return [];
                }),
            ImportColumn::make('productFunctions')
                ->castStateUsing(function ($state): array {
                    if (empty($state)) {
                        return [];
                    }
                    // If it's already a JSON string, decode it first
                    if (is_string($state) && str_starts_with(mb_trim($state), '[')) {
                        $state = json_decode($state, true);
                    }
                    // If it's a string, split by comma
                    if (is_string($state)) {
                        return array_map('trim', explode(',', $state));
                    }
                    // If it's already an array, return it
                    if (is_array($state)) {
                        return array_map('trim', $state);
                    }

                    return [];
                }),
            ImportColumn::make('lzo_tvcop_sensor')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('temperature_sensor')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('humidity_sensor')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('light_sensor')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('hepa_filter_class'),
            ImportColumn::make('effectiveness_hepa_filter')
                ->numeric(),
            ImportColumn::make('price_date'),
            ImportColumn::make('ranking_hidden')
                ->castStateUsing(ImportBooleanCaster::closure()),
            ImportColumn::make('filter_costs'),
            ImportColumn::make('functions_and_equipment'),
            ImportColumn::make('heating_and_cooling_function')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('main_ranking')
                ->castStateUsing(ImportBooleanCaster::closure()),
            ImportColumn::make('for_kids')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('cooling_function')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('bedroom')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('smokers')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('office')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('kindergarten')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('astmatic')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('alergic')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('type_of_device'),
            ImportColumn::make('type')
                ->requiredMapping()
                ->boolean(),
            ImportColumn::make('is_promo')
                ->castStateUsing(ImportBooleanCaster::closure())
                ->requiredMapping()
                ->boolean(),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your air purifier import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    public function resolveRecord(): ?AirPurifier
    {
        return AirPurifier::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'id' => $this->data['remote_id'],
        ]);
    }
}
