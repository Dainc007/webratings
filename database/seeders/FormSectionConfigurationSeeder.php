<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FormSectionConfiguration;
use Illuminate\Database\Seeder;

final class FormSectionConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = $this->getConfigurations();

        foreach ($configurations as $tableName => $sections) {
            foreach ($sections as $sortOrder => $section) {
                FormSectionConfiguration::firstOrCreate(
                    [
                        'table_name' => $tableName,
                        'tab_key' => $section['tab_key'],
                        'section_key' => $section['section_key'],
                    ],
                    [
                        'section_label' => $section['section_label'],
                        'sort_order' => $sortOrder,
                        'columns' => $section['columns'] ?? 1,
                        'is_collapsible' => $section['is_collapsible'] ?? false,
                        'is_visible' => true,
                        'depends_on' => $section['depends_on'] ?? null,
                    ]
                );
            }
        }
    }

    private function getConfigurations(): array
    {
        return [
            'air_purifiers' => [
                ['tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking', 'section_label' => 'Oceny i ranking', 'columns' => 2],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie', 'section_label' => 'Linki partnerskie', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'ceneo', 'section_label' => 'Ceneo', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'galeria', 'section_label' => 'Galeria', 'is_collapsible' => true],
                ['tab_key' => 'humidification', 'section_key' => 'nawilzanie', 'section_label' => 'Nawilżanie', 'columns' => 2],
                ['tab_key' => 'humidification', 'section_key' => 'higrostat', 'section_label' => 'Higrostat', 'columns' => 2],
                ['tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny', 'section_label' => 'Filtr ewaporacyjny', 'is_collapsible' => true, 'depends_on' => 'evaporative_filter'],
                ['tab_key' => 'filters', 'section_key' => 'filtr_hepa', 'section_label' => 'Filtr HEPA', 'is_collapsible' => true, 'depends_on' => 'hepa_filter'],
                ['tab_key' => 'filters', 'section_key' => 'filtr_weglowy', 'section_label' => 'Filtr węglowy', 'is_collapsible' => true, 'depends_on' => 'carbon_filter'],
                ['tab_key' => 'functions', 'section_key' => 'jonizator', 'section_label' => 'Jonizator', 'depends_on' => 'ionization'],
                ['tab_key' => 'functions', 'section_key' => 'inne_funkcje', 'section_label' => 'Inne funkcje', 'is_collapsible' => true],
                ['tab_key' => 'functions', 'section_key' => 'czujniki', 'section_label' => 'Czujniki', 'is_collapsible' => true],
            ],
            'air_humidifiers' => [
                ['tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje', 'section_label' => 'Podstawowe informacje', 'columns' => 2],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie', 'section_label' => 'Linki partnerskie', 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_ceneo', 'section_label' => 'Linki Ceneo', 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji', 'section_label' => 'Link do recenzji', 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'ranking', 'section_label' => 'Ranking', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'typy_i_kategorie', 'section_label' => 'Typy i kategorie', 'is_collapsible' => true],
                ['tab_key' => 'performance', 'section_key' => 'wydajnosc', 'section_label' => 'Wydajność', 'columns' => 2],
                ['tab_key' => 'performance', 'section_key' => 'glosnosc_wentylatora', 'section_label' => 'Głośność wentylatora', 'columns' => 2],
                ['tab_key' => 'performance', 'section_key' => 'pobor_mocy', 'section_label' => 'Pobór mocy', 'columns' => 2],
                ['tab_key' => 'water_tank', 'section_key' => 'zbiornik_na_wode', 'section_label' => 'Zbiornik na wodę', 'columns' => 2],
                ['tab_key' => 'controls', 'section_key' => 'funkcje_smart', 'section_label' => 'Funkcje smart', 'columns' => 2],
                ['tab_key' => 'dimensions', 'section_key' => 'wymiary', 'section_label' => 'Wymiary', 'columns' => 2],
                ['tab_key' => 'categories', 'section_key' => 'kategorie', 'section_label' => 'Kategorie', 'columns' => 2],
                ['tab_key' => 'additional', 'section_key' => 'dodatkowe', 'section_label' => 'Dodatkowe', 'columns' => 2],
                ['tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny', 'section_label' => 'Filtr ewaporacyjny'],
                ['tab_key' => 'filters', 'section_key' => 'srebrna_jonizacja', 'section_label' => 'Srebrna jonizacja'],
                ['tab_key' => 'filters', 'section_key' => 'filtr_ceramiczny', 'section_label' => 'Filtr ceramiczny'],
                ['tab_key' => 'filters', 'section_key' => 'inne_filtry', 'section_label' => 'Inne filtry'],
            ],
            'air_conditioners' => [
                ['tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje', 'section_label' => 'Podstawowe informacje', 'columns' => 2],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie', 'section_label' => 'Linki partnerskie', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_ceneo', 'section_label' => 'Linki Ceneo', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji', 'section_label' => 'Link do recenzji', 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'galeria', 'section_label' => 'Galeria', 'is_collapsible' => true],
                ['tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia', 'section_label' => 'Parametry chłodzenia', 'columns' => 2],
                ['tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania', 'section_label' => 'Parametry grzania', 'columns' => 2],
                ['tab_key' => 'modes_and_functions', 'section_key' => 'tryby_pracy', 'section_label' => 'Tryby pracy', 'columns' => 2],
                ['tab_key' => 'modes_and_functions', 'section_key' => 'parametry_powietrza', 'section_label' => 'Parametry powietrza', 'columns' => 2],
                ['tab_key' => 'modes_and_functions', 'section_key' => 'halas', 'section_label' => 'Hałas', 'columns' => 2],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'filtry_podstawowe', 'section_label' => 'Filtry podstawowe'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa', 'section_label' => 'Filtr HEPA', 'columns' => 2, 'is_collapsible' => true, 'depends_on' => 'hepa_filter'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy', 'section_label' => 'Filtr węglowy', 'columns' => 2, 'is_collapsible' => true, 'depends_on' => 'carbon_filter'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie', 'section_label' => 'Dodatkowe technologie', 'columns' => 2],
                ['tab_key' => 'controls_and_connectivity', 'section_key' => 'sterowanie', 'section_label' => 'Sterowanie', 'columns' => 2],
                ['tab_key' => 'controls_and_connectivity', 'section_key' => 'funkcje_i_wyposazenie', 'section_label' => 'Funkcje i wyposażenie'],
                ['tab_key' => 'technical_specs', 'section_key' => 'chlodziwo', 'section_label' => 'Chłodziwo', 'columns' => 2],
                ['tab_key' => 'technical_specs', 'section_key' => 'parametry_elektryczne', 'section_label' => 'Parametry elektryczne', 'columns' => 2],
                ['tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga', 'section_label' => 'Wymiary i waga', 'columns' => 2],
                ['tab_key' => 'technical_specs', 'section_key' => 'instalacja', 'section_label' => 'Instalacja', 'columns' => 2],
                ['tab_key' => 'additional_info', 'section_key' => 'wyglad', 'section_label' => 'Wygląd'],
                ['tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking', 'section_label' => 'Oceny i ranking', 'columns' => 2],
                ['tab_key' => 'additional_info', 'section_key' => 'dokumentacja', 'section_label' => 'Dokumentacja'],
                ['tab_key' => 'additional_info', 'section_key' => 'dane_systemowe', 'section_label' => 'Dane systemowe', 'columns' => 2, 'is_collapsible' => true],
            ],
            'dehumidifiers' => [
                ['tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje', 'section_label' => 'Podstawowe informacje', 'columns' => 2],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie', 'section_label' => 'Linki partnerskie', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_ceneo', 'section_label' => 'Linki Ceneo', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji', 'section_label' => 'Link do recenzji', 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking', 'section_label' => 'Oceny i ranking', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania', 'section_label' => 'Parametry osuszania', 'columns' => 2],
                ['tab_key' => 'working_conditions', 'section_key' => 'zakres_temperatur', 'section_label' => 'Zakres temperatur', 'columns' => 2],
                ['tab_key' => 'working_conditions', 'section_key' => 'zakres_wilgotnosci', 'section_label' => 'Zakres wilgotności', 'columns' => 2],
                ['tab_key' => 'water_tank', 'section_key' => 'parametry_zbiornika', 'section_label' => 'Parametry zbiornika', 'columns' => 2],
                ['tab_key' => 'hygrostat_and_controls', 'section_key' => 'higrostat', 'section_label' => 'Higrostat', 'columns' => 2],
                ['tab_key' => 'hygrostat_and_controls', 'section_key' => 'wentylator', 'section_label' => 'Wentylator', 'columns' => 2],
                ['tab_key' => 'hygrostat_and_controls', 'section_key' => 'halas', 'section_label' => 'Hałas', 'columns' => 2],
                ['tab_key' => 'hygrostat_and_controls', 'section_key' => 'tryby_pracy', 'section_label' => 'Tryby pracy'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'filtry_podstawowe', 'section_label' => 'Filtry podstawowe'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa', 'section_label' => 'Filtr HEPA', 'columns' => 2, 'is_collapsible' => true, 'depends_on' => 'hepa_filter'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy', 'section_label' => 'Filtr węglowy', 'columns' => 2, 'is_collapsible' => true, 'depends_on' => 'carbon_filter'],
                ['tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie', 'section_label' => 'Dodatkowe technologie', 'columns' => 2],
                ['tab_key' => 'controls_and_connectivity', 'section_key' => 'sterowanie', 'section_label' => 'Sterowanie', 'columns' => 2],
                ['tab_key' => 'controls_and_connectivity', 'section_key' => 'funkcje_i_wyposazenie', 'section_label' => 'Funkcje i wyposażenie'],
                ['tab_key' => 'technical_specs', 'section_key' => 'chlodziwo', 'section_label' => 'Chłodziwo', 'columns' => 2],
                ['tab_key' => 'technical_specs', 'section_key' => 'parametry_elektryczne', 'section_label' => 'Parametry elektryczne', 'columns' => 2],
                ['tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga', 'section_label' => 'Wymiary i waga', 'columns' => 2],
                ['tab_key' => 'additional_info', 'section_key' => 'galeria_i_dokumentacja', 'section_label' => 'Galeria i dokumentacja'],
                ['tab_key' => 'additional_info', 'section_key' => 'dane_systemowe', 'section_label' => 'Dane systemowe', 'columns' => 2, 'is_collapsible' => true],
            ],
            'sensors' => [
                ['tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje', 'section_label' => 'Podstawowe informacje', 'columns' => 2],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie', 'section_label' => 'Linki partnerskie', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'ceneo', 'section_label' => 'Ceneo', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji', 'section_label' => 'Link do recenzji', 'is_collapsible' => true],
                ['tab_key' => 'pm_sensors', 'section_key' => 'pm1_sensor', 'section_label' => 'Czujnik PM1', 'columns' => 2],
                ['tab_key' => 'pm_sensors', 'section_key' => 'pm2_sensor', 'section_label' => 'Czujnik PM2.5', 'columns' => 2],
                ['tab_key' => 'pm_sensors', 'section_key' => 'pm10_sensor', 'section_label' => 'Czujnik PM10', 'columns' => 2],
                ['tab_key' => 'chemical_sensors', 'section_key' => 'lzo_sensor', 'section_label' => 'Czujnik LZO', 'columns' => 2],
                ['tab_key' => 'chemical_sensors', 'section_key' => 'hcho_sensor', 'section_label' => 'Czujnik HCHO', 'columns' => 2],
                ['tab_key' => 'chemical_sensors', 'section_key' => 'co2_sensor', 'section_label' => 'Czujnik CO2', 'columns' => 2],
                ['tab_key' => 'chemical_sensors', 'section_key' => 'co_sensor', 'section_label' => 'Czujnik CO', 'columns' => 2],
                ['tab_key' => 'environmental_sensors', 'section_key' => 'temperature_sensor', 'section_label' => 'Czujnik temperatury', 'columns' => 2],
                ['tab_key' => 'environmental_sensors', 'section_key' => 'humidity_sensor', 'section_label' => 'Czujnik wilgotności', 'columns' => 2],
                ['tab_key' => 'environmental_sensors', 'section_key' => 'pressure_sensor', 'section_label' => 'Czujnik ciśnienia', 'columns' => 2],
                ['tab_key' => 'power_and_connectivity', 'section_key' => 'power', 'section_label' => 'Zasilanie', 'columns' => 2],
                ['tab_key' => 'power_and_connectivity', 'section_key' => 'connectivity', 'section_label' => 'Łączność', 'columns' => 2],
                ['tab_key' => 'device_features', 'section_key' => 'features', 'section_label' => 'Funkcje'],
                ['tab_key' => 'dimensions_and_performance', 'section_key' => 'physical_dimensions', 'section_label' => 'Wymiary fizyczne', 'columns' => 2],
                ['tab_key' => 'dimensions_and_performance', 'section_key' => 'performance_rating', 'section_label' => 'Ocena wydajności', 'columns' => 2],
                ['tab_key' => 'ranking', 'section_key' => 'ranking_settings', 'section_label' => 'Ustawienia rankingu', 'columns' => 2],
                ['tab_key' => 'metadata', 'section_key' => 'system_identifiers', 'section_label' => 'Identyfikatory systemowe', 'columns' => 2],
                ['tab_key' => 'metadata', 'section_key' => 'timestamps', 'section_label' => 'Znaczniki czasu', 'columns' => 2],
            ],
            'upright_vacuums' => [
                ['tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje', 'section_label' => 'Podstawowe informacje', 'columns' => 2],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie', 'section_label' => 'Linki partnerskie', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'linki_ceneo', 'section_label' => 'Linki Ceneo', 'columns' => 2, 'is_collapsible' => true],
                ['tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji', 'section_label' => 'Link do recenzji', 'is_collapsible' => true],
                ['tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania', 'section_label' => 'Parametry ssania', 'columns' => 2],
                ['tab_key' => 'power_and_performance', 'section_key' => 'silnik', 'section_label' => 'Silnik', 'columns' => 2],
                ['tab_key' => 'power_and_battery', 'section_key' => 'zasilanie', 'section_label' => 'Zasilanie', 'columns' => 2],
                ['tab_key' => 'power_and_battery', 'section_key' => 'bateria', 'section_label' => 'Bateria', 'columns' => 2],
                ['tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania', 'section_label' => 'Funkcje mopowania', 'columns' => 2],
                ['tab_key' => 'cleaning_functions', 'section_key' => 'zbiorniki', 'section_label' => 'Zbiorniki', 'columns' => 2],
                ['tab_key' => 'filters_and_technologies', 'section_key' => 'system_filtracji', 'section_label' => 'System filtracji', 'columns' => 2],
                ['tab_key' => 'filters_and_technologies', 'section_key' => 'dodatkowe_technologie', 'section_label' => 'Dodatkowe technologie', 'columns' => 2],
                ['tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki', 'section_label' => 'Szczotki', 'columns' => 2],
                ['tab_key' => 'brushes_and_accessories', 'section_key' => 'wyposazenie_dodatkowe', 'section_label' => 'Wyposażenie dodatkowe', 'columns' => 2],
                ['tab_key' => 'display_and_controls', 'section_key' => 'wyswietlacz', 'section_label' => 'Wyświetlacz', 'columns' => 2],
                ['tab_key' => 'display_and_controls', 'section_key' => 'czas_pracy', 'section_label' => 'Czas pracy', 'columns' => 2],
                ['tab_key' => 'additional_info', 'section_key' => 'wyglad_i_wymiary', 'section_label' => 'Wygląd i wymiary', 'columns' => 2],
                ['tab_key' => 'additional_info', 'section_key' => 'przeznaczenie', 'section_label' => 'Przeznaczenie', 'columns' => 2],
                ['tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking', 'section_label' => 'Oceny i ranking', 'columns' => 2],
            ],
        ];
    }
}
