<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FormTabConfiguration;
use Illuminate\Database\Seeder;

final class FormTabConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            'air_purifiers' => [
                ['tab_key' => 'basic_info', 'tab_label' => 'Podstawowe informacje'],
                ['tab_key' => 'performance', 'tab_label' => 'Wydajność', 'columns' => 4],
                ['tab_key' => 'humidification', 'tab_label' => 'Nawilżanie'],
                ['tab_key' => 'filters', 'tab_label' => 'Filtry'],
                ['tab_key' => 'functions', 'tab_label' => 'Funkcje'],
                ['tab_key' => 'dimensions', 'tab_label' => 'Wymiary', 'columns' => 4],
                ['tab_key' => 'classification', 'tab_label' => 'Klasyfikacja'],
                ['tab_key' => 'dates', 'tab_label' => 'Daty'],
                ['tab_key' => 'custom_fields', 'tab_label' => 'Dodatkowe pola'],
            ],
            'air_humidifiers' => [
                ['tab_key' => 'basic_info', 'tab_label' => 'Podstawowe informacje'],
                ['tab_key' => 'performance', 'tab_label' => 'Wydajność'],
                ['tab_key' => 'water_tank', 'tab_label' => 'Zbiornik na wodę'],
                ['tab_key' => 'controls', 'tab_label' => 'Sterowanie'],
                ['tab_key' => 'filters', 'tab_label' => 'Filtry'],
                ['tab_key' => 'dimensions', 'tab_label' => 'Wymiary'],
                ['tab_key' => 'categories', 'tab_label' => 'Kategorie'],
                ['tab_key' => 'additional', 'tab_label' => 'Dodatkowe'],
                ['tab_key' => 'custom_fields', 'tab_label' => 'Dodatkowe pola'],
            ],
            'air_conditioners' => [
                ['tab_key' => 'basic_info', 'tab_label' => 'Podstawowe informacje'],
                ['tab_key' => 'cooling_performance', 'tab_label' => 'Wydajność chłodzenia'],
                ['tab_key' => 'heating_performance', 'tab_label' => 'Wydajność grzania'],
                ['tab_key' => 'modes_and_functions', 'tab_label' => 'Tryby pracy i funkcje'],
                ['tab_key' => 'filters_and_purification', 'tab_label' => 'Filtry i oczyszczanie'],
                ['tab_key' => 'controls_and_connectivity', 'tab_label' => 'Sterowanie i łączność'],
                ['tab_key' => 'technical_specs', 'tab_label' => 'Specyfikacja techniczna'],
                ['tab_key' => 'additional_info', 'tab_label' => 'Dodatkowe informacje'],
                ['tab_key' => 'custom_fields', 'tab_label' => 'Dodatkowe pola'],
            ],
            'dehumidifiers' => [
                ['tab_key' => 'basic_info', 'tab_label' => 'Podstawowe informacje'],
                ['tab_key' => 'drying_performance', 'tab_label' => 'Wydajność osuszania'],
                ['tab_key' => 'working_conditions', 'tab_label' => 'Warunki pracy'],
                ['tab_key' => 'water_tank', 'tab_label' => 'Zbiornik na wodę'],
                ['tab_key' => 'hygrostat_and_controls', 'tab_label' => 'Higrostat i sterowanie'],
                ['tab_key' => 'filters_and_purification', 'tab_label' => 'Filtry i oczyszczanie'],
                ['tab_key' => 'controls_and_connectivity', 'tab_label' => 'Sterowanie i łączność'],
                ['tab_key' => 'technical_specs', 'tab_label' => 'Specyfikacja techniczna'],
                ['tab_key' => 'additional_info', 'tab_label' => 'Dodatkowe informacje'],
                ['tab_key' => 'custom_fields', 'tab_label' => 'Dodatkowe pola'],
            ],
            'sensors' => [
                ['tab_key' => 'basic_info', 'tab_label' => 'Podstawowe informacje'],
                ['tab_key' => 'pm_sensors', 'tab_label' => 'Czujniki PM'],
                ['tab_key' => 'chemical_sensors', 'tab_label' => 'Czujniki chemiczne'],
                ['tab_key' => 'environmental_sensors', 'tab_label' => 'Czujniki środowiskowe'],
                ['tab_key' => 'power_and_connectivity', 'tab_label' => 'Zasilanie i łączność'],
                ['tab_key' => 'device_features', 'tab_label' => 'Funkcje urządzenia'],
                ['tab_key' => 'dimensions_and_performance', 'tab_label' => 'Wymiary i wydajność'],
                ['tab_key' => 'ranking', 'tab_label' => 'Ranking'],
                ['tab_key' => 'metadata', 'tab_label' => 'Metadane'],
                ['tab_key' => 'custom_fields', 'tab_label' => 'Dodatkowe pola'],
            ],
            'upright_vacuums' => [
                ['tab_key' => 'basic_info', 'tab_label' => 'Podstawowe informacje'],
                ['tab_key' => 'power_and_performance', 'tab_label' => 'Moc i wydajność'],
                ['tab_key' => 'power_and_battery', 'tab_label' => 'Zasilanie i bateria'],
                ['tab_key' => 'cleaning_functions', 'tab_label' => 'Funkcje czyszczenia'],
                ['tab_key' => 'filters_and_technologies', 'tab_label' => 'Filtry i technologie'],
                ['tab_key' => 'brushes_and_accessories', 'tab_label' => 'Szczotki i akcesoria'],
                ['tab_key' => 'display_and_controls', 'tab_label' => 'Wyświetlacz i sterowanie'],
                ['tab_key' => 'additional_info', 'tab_label' => 'Dodatkowe informacje'],
                ['tab_key' => 'custom_fields', 'tab_label' => 'Dodatkowe pola'],
            ],
        ];

        foreach ($configurations as $tableName => $tabs) {
            foreach ($tabs as $sortOrder => $tab) {
                FormTabConfiguration::firstOrCreate(
                    [
                        'table_name' => $tableName,
                        'tab_key' => $tab['tab_key'],
                    ],
                    [
                        'tab_label' => $tab['tab_label'],
                        'sort_order' => $sortOrder,
                        'is_visible' => true,
                        'columns' => $tab['columns'] ?? null,
                    ]
                );
            }
        }
    }
}
