<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FormFieldConfiguration;
use Illuminate\Database\Seeder;

final class FormFieldConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = $this->getConfigurations();

        foreach ($configurations as $tableName => $fields) {
            foreach ($fields as $sortOrder => $field) {
                FormFieldConfiguration::firstOrCreate(
                    [
                        'table_name' => $tableName,
                        'field_name' => $field['field_name'],
                    ],
                    [
                        'tab_key' => $field['tab_key'],
                        'section_key' => $field['section_key'] ?? null,
                        'sort_order' => $sortOrder,
                        'is_visible' => true,
                    ]
                );
            }
        }
    }

    private function getConfigurations(): array
    {
        return [
            'air_purifiers' => [
                // basic_info tab - loose fields
                ['field_name' => 'status', 'tab_key' => 'basic_info'],
                ['field_name' => 'model', 'tab_key' => 'basic_info'],
                ['field_name' => 'brand_name', 'tab_key' => 'basic_info'],
                ['field_name' => 'price', 'tab_key' => 'basic_info'],
                ['field_name' => 'price_before', 'tab_key' => 'basic_info'],
                ['field_name' => 'discount_info', 'tab_key' => 'basic_info'],
                ['field_name' => 'price_date', 'tab_key' => 'basic_info'],
                // basic_info tab - oceny_i_ranking section
                ['field_name' => 'capability_points', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'profitability_points', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'popularity', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                // basic_info tab - linki_partnerskie section
                ['field_name' => 'partner_link_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                // basic_info tab - ceneo section
                ['field_name' => 'ceneo_url', 'tab_key' => 'basic_info', 'section_key' => 'ceneo'],
                ['field_name' => 'ceneo_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'ceneo'],
                // basic_info tab - galeria section
                ['field_name' => 'gallery', 'tab_key' => 'basic_info', 'section_key' => 'galeria'],
                // performance tab - loose fields
                ['field_name' => 'max_performance', 'tab_key' => 'performance'],
                ['field_name' => 'max_area', 'tab_key' => 'performance'],
                ['field_name' => 'max_area_ro', 'tab_key' => 'performance'],
                ['field_name' => 'number_of_fan_speeds', 'tab_key' => 'performance'],
                ['field_name' => 'min_loudness', 'tab_key' => 'performance'],
                ['field_name' => 'max_loudness', 'tab_key' => 'performance'],
                ['field_name' => 'min_rated_power_consumption', 'tab_key' => 'performance'],
                ['field_name' => 'max_rated_power_consumption', 'tab_key' => 'performance'],
                // humidification tab - nawilzanie section
                ['field_name' => 'has_humidification', 'tab_key' => 'humidification', 'section_key' => 'nawilzanie'],
                ['field_name' => 'humidification_type', 'tab_key' => 'humidification', 'section_key' => 'nawilzanie'],
                ['field_name' => 'humidification_switch', 'tab_key' => 'humidification', 'section_key' => 'nawilzanie'],
                ['field_name' => 'humidification_area', 'tab_key' => 'humidification', 'section_key' => 'nawilzanie'],
                ['field_name' => 'water_tank_capacity', 'tab_key' => 'humidification', 'section_key' => 'nawilzanie'],
                ['field_name' => 'humidification_efficiency', 'tab_key' => 'humidification', 'section_key' => 'nawilzanie'],
                // humidification tab - higrostat section
                ['field_name' => 'hygrometer', 'tab_key' => 'humidification', 'section_key' => 'higrostat'],
                ['field_name' => 'hygrostat', 'tab_key' => 'humidification', 'section_key' => 'higrostat'],
                ['field_name' => 'hygrostat_min', 'tab_key' => 'humidification', 'section_key' => 'higrostat'],
                ['field_name' => 'hygrostat_max', 'tab_key' => 'humidification', 'section_key' => 'higrostat'],
                // filters tab - filtr_ewaporacyjny section
                ['field_name' => 'evaporative_filter', 'tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny'],
                ['field_name' => 'evaporative_filter_life', 'tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny'],
                ['field_name' => 'evaporative_filter_price', 'tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny'],
                // filters tab - filtr_hepa section
                ['field_name' => 'hepa_filter', 'tab_key' => 'filters', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_filter_class', 'tab_key' => 'filters', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'effectiveness_hepa_filter', 'tab_key' => 'filters', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_filter_service_life', 'tab_key' => 'filters', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_filter_price', 'tab_key' => 'filters', 'section_key' => 'filtr_hepa'],
                // filters tab - filtr_weglowy section
                ['field_name' => 'carbon_filter', 'tab_key' => 'filters', 'section_key' => 'filtr_weglowy'],
                ['field_name' => 'carbon_filter_service_life', 'tab_key' => 'filters', 'section_key' => 'filtr_weglowy'],
                ['field_name' => 'carbon_filter_price', 'tab_key' => 'filters', 'section_key' => 'filtr_weglowy'],
                // filters tab - loose fields
                ['field_name' => 'mesh_filter', 'tab_key' => 'filters'],
                ['field_name' => 'filter_costs', 'tab_key' => 'filters'],
                // functions tab - jonizator section
                ['field_name' => 'ionization', 'tab_key' => 'functions', 'section_key' => 'jonizator'],
                ['field_name' => 'ionizer_type', 'tab_key' => 'functions', 'section_key' => 'jonizator'],
                ['field_name' => 'ionizer_switch', 'tab_key' => 'functions', 'section_key' => 'jonizator'],
                // functions tab - inne_funkcje section
                ['field_name' => 'uvc', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                ['field_name' => 'mobile_app', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                ['field_name' => 'remote_control', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                ['field_name' => 'functions_and_equipment', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                ['field_name' => 'heating_and_cooling_function', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                ['field_name' => 'cooling_function', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                ['field_name' => 'certificates', 'tab_key' => 'functions', 'section_key' => 'inne_funkcje'],
                // functions tab - czujniki section
                ['field_name' => 'pm2_sensor', 'tab_key' => 'functions', 'section_key' => 'czujniki'],
                ['field_name' => 'lzo_tvcop_sensor', 'tab_key' => 'functions', 'section_key' => 'czujniki'],
                ['field_name' => 'temperature_sensor', 'tab_key' => 'functions', 'section_key' => 'czujniki'],
                ['field_name' => 'humidity_sensor', 'tab_key' => 'functions', 'section_key' => 'czujniki'],
                ['field_name' => 'light_sensor', 'tab_key' => 'functions', 'section_key' => 'czujniki'],
                // dimensions tab - loose fields
                ['field_name' => 'width', 'tab_key' => 'dimensions'],
                ['field_name' => 'height', 'tab_key' => 'dimensions'],
                ['field_name' => 'depth', 'tab_key' => 'dimensions'],
                ['field_name' => 'weight', 'tab_key' => 'dimensions'],
                ['field_name' => 'colors', 'tab_key' => 'dimensions'],
                ['field_name' => 'type_of_device', 'tab_key' => 'dimensions'],
                // classification tab - loose fields
                ['field_name' => 'main_ranking', 'tab_key' => 'classification'],
                ['field_name' => 'ranking_hidden', 'tab_key' => 'classification'],
                ['field_name' => 'for_kids', 'tab_key' => 'classification'],
                ['field_name' => 'bedroom', 'tab_key' => 'classification'],
                ['field_name' => 'smokers', 'tab_key' => 'classification'],
                ['field_name' => 'office', 'tab_key' => 'classification'],
                ['field_name' => 'kindergarten', 'tab_key' => 'classification'],
                ['field_name' => 'astmatic', 'tab_key' => 'classification'],
                ['field_name' => 'alergic', 'tab_key' => 'classification'],
                // dates tab - loose fields
                ['field_name' => 'date_created', 'tab_key' => 'dates'],
                ['field_name' => 'date_updated', 'tab_key' => 'dates'],
                ['field_name' => 'created_at', 'tab_key' => 'dates'],
                ['field_name' => 'updated_at', 'tab_key' => 'dates'],
            ],
            'air_humidifiers' => [
                // basic_info tab - podstawowe_informacje section
                ['field_name' => 'remote_id', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'status', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'model', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'brand_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price_before', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'discount_info', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                // basic_info tab - linki_partnerskie section
                ['field_name' => 'partner_link_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                // basic_info tab - linki_ceneo section
                ['field_name' => 'ceneo_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                // basic_info tab - link_do_recenzji section
                ['field_name' => 'review_link', 'tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji'],
                // basic_info tab - ranking section
                ['field_name' => 'capability', 'tab_key' => 'basic_info', 'section_key' => 'ranking'],
                ['field_name' => 'ranking', 'tab_key' => 'basic_info', 'section_key' => 'ranking'],
                ['field_name' => 'profitability', 'tab_key' => 'basic_info', 'section_key' => 'ranking'],
                ['field_name' => 'ranking_hidden', 'tab_key' => 'basic_info', 'section_key' => 'ranking'],
                ['field_name' => 'main_ranking', 'tab_key' => 'basic_info', 'section_key' => 'ranking'],
                // basic_info tab - typy_i_kategorie section
                ['field_name' => 'types', 'tab_key' => 'basic_info', 'section_key' => 'typy_i_kategorie'],
                ['field_name' => 'type_of_device', 'tab_key' => 'basic_info', 'section_key' => 'typy_i_kategorie'],
                // performance tab - wydajnosc section
                ['field_name' => 'max_performance', 'tab_key' => 'performance', 'section_key' => 'wydajnosc'],
                ['field_name' => 'max_area', 'tab_key' => 'performance', 'section_key' => 'wydajnosc'],
                ['field_name' => 'max_area_ro', 'tab_key' => 'performance', 'section_key' => 'wydajnosc'],
                ['field_name' => 'tested_efficiency', 'tab_key' => 'performance', 'section_key' => 'wydajnosc'],
                // performance tab - glosnosc_wentylatora section
                ['field_name' => 'fan_volume', 'tab_key' => 'performance', 'section_key' => 'glosnosc_wentylatora'],
                ['field_name' => 'min_fan_volume', 'tab_key' => 'performance', 'section_key' => 'glosnosc_wentylatora'],
                ['field_name' => 'max_fan_volume', 'tab_key' => 'performance', 'section_key' => 'glosnosc_wentylatora'],
                // performance tab - pobor_mocy section
                ['field_name' => 'min_rated_power_consumption', 'tab_key' => 'performance', 'section_key' => 'pobor_mocy'],
                ['field_name' => 'max_rated_power_consumption', 'tab_key' => 'performance', 'section_key' => 'pobor_mocy'],
                // water_tank tab - zbiornik_na_wode section
                ['field_name' => 'water_tank_capacity', 'tab_key' => 'water_tank', 'section_key' => 'zbiornik_na_wode'],
                ['field_name' => 'water_tank_min_time', 'tab_key' => 'water_tank', 'section_key' => 'zbiornik_na_wode'],
                ['field_name' => 'water_tank_fill_type', 'tab_key' => 'water_tank', 'section_key' => 'zbiornik_na_wode'],
                // controls tab - funkcje_smart section
                ['field_name' => 'hygrostat', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'hygrostat_min', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'hygrostat_max', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'timer', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'timer_min', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'timer_max', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'auto_mode', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'night_mode', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'night_mode_min', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'night_mode_max', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'child_lock', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'child_lock_min', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'child_lock_max', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'display', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'display_min', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'display_max', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'remote_control', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'remote_control_min', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'remote_control_max', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'productFunctions', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'mobile_app', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                ['field_name' => 'mobile_features', 'tab_key' => 'controls', 'section_key' => 'funkcje_smart'],
                // dimensions tab - wymiary section
                ['field_name' => 'rated_voltage', 'tab_key' => 'dimensions', 'section_key' => 'wymiary'],
                ['field_name' => 'width', 'tab_key' => 'dimensions', 'section_key' => 'wymiary'],
                ['field_name' => 'height', 'tab_key' => 'dimensions', 'section_key' => 'wymiary'],
                ['field_name' => 'weight', 'tab_key' => 'dimensions', 'section_key' => 'wymiary'],
                ['field_name' => 'depth', 'tab_key' => 'dimensions', 'section_key' => 'wymiary'],
                // categories tab - kategorie section
                ['field_name' => 'for_plant', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                ['field_name' => 'for_desk', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                ['field_name' => 'alergic', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                ['field_name' => 'astmatic', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                ['field_name' => 'small', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                ['field_name' => 'for_kids', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                ['field_name' => 'big_area', 'tab_key' => 'categories', 'section_key' => 'kategorie'],
                // additional tab - dodatkowe section
                ['field_name' => 'colors', 'tab_key' => 'additional', 'section_key' => 'dodatkowe'],
                ['field_name' => 'gallery', 'tab_key' => 'additional', 'section_key' => 'dodatkowe'],
                ['field_name' => 'disks', 'tab_key' => 'additional', 'section_key' => 'dodatkowe'],
                // filters tab - filtr_ewaporacyjny section
                ['field_name' => 'evaporative_filter', 'tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny'],
                ['field_name' => 'evaporative_filter_life', 'tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny'],
                ['field_name' => 'evaporative_filter_price', 'tab_key' => 'filters', 'section_key' => 'filtr_ewaporacyjny'],
                // filters tab - srebrna_jonizacja section
                ['field_name' => 'silver_ion', 'tab_key' => 'filters', 'section_key' => 'srebrna_jonizacja'],
                ['field_name' => 'silver_ion_life', 'tab_key' => 'filters', 'section_key' => 'srebrna_jonizacja'],
                ['field_name' => 'silver_ion_price', 'tab_key' => 'filters', 'section_key' => 'srebrna_jonizacja'],
                // filters tab - filtr_ceramiczny section
                ['field_name' => 'ceramic_filter', 'tab_key' => 'filters', 'section_key' => 'filtr_ceramiczny'],
                ['field_name' => 'ceramic_filter_life', 'tab_key' => 'filters', 'section_key' => 'filtr_ceramiczny'],
                ['field_name' => 'ceramic_filter_price', 'tab_key' => 'filters', 'section_key' => 'filtr_ceramiczny'],
                // filters tab - inne_filtry section
                ['field_name' => 'uv_lamp', 'tab_key' => 'filters', 'section_key' => 'inne_filtry'],
                ['field_name' => 'ionization', 'tab_key' => 'filters', 'section_key' => 'inne_filtry'],
                ['field_name' => 'hepa_filter_class', 'tab_key' => 'filters', 'section_key' => 'inne_filtry'],
                ['field_name' => 'mesh_filter', 'tab_key' => 'filters', 'section_key' => 'inne_filtry'],
                ['field_name' => 'carbon_filter', 'tab_key' => 'filters', 'section_key' => 'inne_filtry'],
            ],
            'air_conditioners' => [
                // basic_info tab - podstawowe_informacje section
                ['field_name' => 'status', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'model', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'brand_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'type', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price_before', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'image', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'discount_info', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'partner_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                // basic_info tab - linki_partnerskie section
                ['field_name' => 'partner_link_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                // basic_info tab - linki_ceneo section
                ['field_name' => 'ceneo_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                ['field_name' => 'ceneo_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                ['field_name' => 'ceneo_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                // basic_info tab - link_do_recenzji section
                ['field_name' => 'review_link', 'tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji'],
                // basic_info tab - galeria section
                ['field_name' => 'gallery', 'tab_key' => 'basic_info', 'section_key' => 'galeria'],
                // cooling_performance tab - parametry_chlodzenia section
                ['field_name' => 'maximum_cooling_power', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'max_cooling_area_manufacturer', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'max_cooling_area_ro', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'max_cooling_temperature', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'min_cooling_temperature', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'cooling_energy_class', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'eer', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'rated_power_cooling_consumption', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                ['field_name' => 'mode_cooling', 'tab_key' => 'cooling_performance', 'section_key' => 'parametry_chlodzenia'],
                // heating_performance tab - parametry_grzania section
                ['field_name' => 'maximum_heating_power', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'max_heating_area_manufacturer', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'max_heating_area_ro', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'max_heating_temperature', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'min_heating_temperature', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'heating_energy_class', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'cop', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'rated_power_heating_consumption', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                ['field_name' => 'mode_heating', 'tab_key' => 'heating_performance', 'section_key' => 'parametry_grzania'],
                // modes_and_functions tab - tryby_pracy section
                ['field_name' => 'mode_dry', 'tab_key' => 'modes_and_functions', 'section_key' => 'tryby_pracy'],
                ['field_name' => 'max_performance_dry', 'tab_key' => 'modes_and_functions', 'section_key' => 'tryby_pracy'],
                ['field_name' => 'max_performance_dry_condition', 'tab_key' => 'modes_and_functions', 'section_key' => 'tryby_pracy'],
                ['field_name' => 'mode_fan', 'tab_key' => 'modes_and_functions', 'section_key' => 'tryby_pracy'],
                ['field_name' => 'mode_purify', 'tab_key' => 'modes_and_functions', 'section_key' => 'tryby_pracy'],
                // modes_and_functions tab - parametry_powietrza section
                ['field_name' => 'max_air_flow', 'tab_key' => 'modes_and_functions', 'section_key' => 'parametry_powietrza'],
                ['field_name' => 'number_of_fan_speeds', 'tab_key' => 'modes_and_functions', 'section_key' => 'parametry_powietrza'],
                ['field_name' => 'swing', 'tab_key' => 'modes_and_functions', 'section_key' => 'parametry_powietrza'],
                ['field_name' => 'temperature_range', 'tab_key' => 'modes_and_functions', 'section_key' => 'parametry_powietrza'],
                // modes_and_functions tab - halas section
                ['field_name' => 'max_loudness', 'tab_key' => 'modes_and_functions', 'section_key' => 'halas'],
                ['field_name' => 'min_loudness', 'tab_key' => 'modes_and_functions', 'section_key' => 'halas'],
                // filters_and_purification tab - filtry_podstawowe section
                ['field_name' => 'mesh_filter', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtry_podstawowe'],
                // filters_and_purification tab - filtr_hepa section
                ['field_name' => 'hepa_filter', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_filter_price', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_service_life', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa'],
                // filters_and_purification tab - filtr_weglowy section
                ['field_name' => 'carbon_filter', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy'],
                ['field_name' => 'carbon_filter_price', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy'],
                ['field_name' => 'carbon_service_life', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy'],
                // filters_and_purification tab - dodatkowe_technologie section
                ['field_name' => 'ionization', 'tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'uvc', 'tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'uv_light_generator', 'tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie'],
                // controls_and_connectivity tab - sterowanie section
                ['field_name' => 'remote_control', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'sterowanie'],
                ['field_name' => 'mobile_app', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'sterowanie'],
                // controls_and_connectivity tab - funkcje_i_wyposazenie section
                ['field_name' => 'productFunctions', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'funkcje_i_wyposazenie'],
                ['field_name' => 'functions_and_equipment_condi', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'funkcje_i_wyposazenie'],
                // technical_specs tab - chlodziwo section
                ['field_name' => 'refrigerant_kind', 'tab_key' => 'technical_specs', 'section_key' => 'chlodziwo'],
                ['field_name' => 'refrigerant_amount', 'tab_key' => 'technical_specs', 'section_key' => 'chlodziwo'],
                ['field_name' => 'needs_to_be_completed', 'tab_key' => 'technical_specs', 'section_key' => 'chlodziwo'],
                // technical_specs tab - parametry_elektryczne section
                ['field_name' => 'rated_voltage', 'tab_key' => 'technical_specs', 'section_key' => 'parametry_elektryczne'],
                // technical_specs tab - wymiary_i_waga section
                ['field_name' => 'width', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                ['field_name' => 'height', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                ['field_name' => 'depth', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                ['field_name' => 'weight', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                // technical_specs tab - instalacja section
                ['field_name' => 'discharge_pipe', 'tab_key' => 'technical_specs', 'section_key' => 'instalacja'],
                ['field_name' => 'discharge_pipe_length', 'tab_key' => 'technical_specs', 'section_key' => 'instalacja'],
                ['field_name' => 'discharge_pipe_diameter', 'tab_key' => 'technical_specs', 'section_key' => 'instalacja'],
                ['field_name' => 'drain_hose', 'tab_key' => 'technical_specs', 'section_key' => 'instalacja'],
                ['field_name' => 'sealing', 'tab_key' => 'technical_specs', 'section_key' => 'instalacja'],
                // additional_info tab - wyglad section
                ['field_name' => 'colors', 'tab_key' => 'additional_info', 'section_key' => 'wyglad'],
                // additional_info tab - oceny_i_ranking section
                ['field_name' => 'capability', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'profitability', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'ranking', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'ranking_hidden', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'main_ranking', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'small', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                // additional_info tab - dokumentacja section
                ['field_name' => 'manual', 'tab_key' => 'additional_info', 'section_key' => 'dokumentacja'],
                // additional_info tab - dane_systemowe section
                ['field_name' => 'remote_id', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'sort', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'user_created', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'date_created', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'user_updated', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'date_updated', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
            ],
            'dehumidifiers' => [
                // basic_info tab - podstawowe_informacje section
                ['field_name' => 'status', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'model', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'brand_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'type', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price_before', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'image', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'discount_info', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'partner_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                // basic_info tab - linki_partnerskie section
                ['field_name' => 'partner_link_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                // basic_info tab - linki_ceneo section
                ['field_name' => 'ceneo_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                ['field_name' => 'ceneo_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                ['field_name' => 'ceneo_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                // basic_info tab - link_do_recenzji section
                ['field_name' => 'review_link', 'tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji'],
                // basic_info tab - oceny_i_ranking section
                ['field_name' => 'capability', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'profitability', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'ranking', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'ranking_hidden', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'main_ranking', 'tab_key' => 'basic_info', 'section_key' => 'oceny_i_ranking'],
                // drying_performance tab - parametry_osuszania section
                ['field_name' => 'max_performance_dry', 'tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania'],
                ['field_name' => 'other_performance_dry', 'tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania'],
                ['field_name' => 'max_performance_dry_condition', 'tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania'],
                ['field_name' => 'other_performance_condition', 'tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania'],
                ['field_name' => 'max_drying_area_manufacturer', 'tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania'],
                ['field_name' => 'max_drying_area_ro', 'tab_key' => 'drying_performance', 'section_key' => 'parametry_osuszania'],
                // working_conditions tab - zakres_temperatur section
                ['field_name' => 'minimum_temperature', 'tab_key' => 'working_conditions', 'section_key' => 'zakres_temperatur'],
                ['field_name' => 'maximum_temperature', 'tab_key' => 'working_conditions', 'section_key' => 'zakres_temperatur'],
                // working_conditions tab - zakres_wilgotnosci section
                ['field_name' => 'minimum_humidity', 'tab_key' => 'working_conditions', 'section_key' => 'zakres_wilgotnosci'],
                ['field_name' => 'maximum_humidity', 'tab_key' => 'working_conditions', 'section_key' => 'zakres_wilgotnosci'],
                // water_tank tab - parametry_zbiornika section
                ['field_name' => 'water_tank_capacity', 'tab_key' => 'water_tank', 'section_key' => 'parametry_zbiornika'],
                ['field_name' => 'minimum_fill_time', 'tab_key' => 'water_tank', 'section_key' => 'parametry_zbiornika'],
                ['field_name' => 'average_filling_time', 'tab_key' => 'water_tank', 'section_key' => 'parametry_zbiornika'],
                // hygrostat_and_controls tab - higrostat section
                ['field_name' => 'higrostat', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'higrostat'],
                ['field_name' => 'min_value_for_hygrostat', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'higrostat'],
                ['field_name' => 'max_value_for_hygrostat', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'higrostat'],
                ['field_name' => 'increment_of_the_hygrostat', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'higrostat'],
                // hygrostat_and_controls tab - wentylator section
                ['field_name' => 'number_of_fan_speeds', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'wentylator'],
                ['field_name' => 'max_air_flow', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'wentylator'],
                // hygrostat_and_controls tab - halas section
                ['field_name' => 'max_loudness', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'halas'],
                ['field_name' => 'min_loudness', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'halas'],
                // hygrostat_and_controls tab - tryby_pracy section
                ['field_name' => 'modes_of_operation', 'tab_key' => 'hygrostat_and_controls', 'section_key' => 'tryby_pracy'],
                // filters_and_purification tab - filtry_podstawowe section
                ['field_name' => 'mesh_filter', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtry_podstawowe'],
                // filters_and_purification tab - filtr_hepa section
                ['field_name' => 'hepa_filter', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_filter_price', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa'],
                ['field_name' => 'hepa_service_life', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_hepa'],
                // filters_and_purification tab - filtr_weglowy section
                ['field_name' => 'carbon_filter', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy'],
                ['field_name' => 'carbon_filter_price', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy'],
                ['field_name' => 'carbon_service_life', 'tab_key' => 'filters_and_purification', 'section_key' => 'filtr_weglowy'],
                // filters_and_purification tab - dodatkowe_technologie section
                ['field_name' => 'ionization', 'tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'uvc', 'tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'uv_light_generator', 'tab_key' => 'filters_and_purification', 'section_key' => 'dodatkowe_technologie'],
                // controls_and_connectivity tab - sterowanie section
                ['field_name' => 'mobile_app', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'sterowanie'],
                ['field_name' => 'mobile_features', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'sterowanie'],
                // controls_and_connectivity tab - funkcje_i_wyposazenie section
                ['field_name' => 'productFunctions', 'tab_key' => 'controls_and_connectivity', 'section_key' => 'funkcje_i_wyposazenie'],
                // technical_specs tab - chlodziwo section
                ['field_name' => 'refrigerant_kind', 'tab_key' => 'technical_specs', 'section_key' => 'chlodziwo'],
                ['field_name' => 'refrigerant_amount', 'tab_key' => 'technical_specs', 'section_key' => 'chlodziwo'],
                ['field_name' => 'needs_to_be_completed', 'tab_key' => 'technical_specs', 'section_key' => 'chlodziwo'],
                // technical_specs tab - parametry_elektryczne section
                ['field_name' => 'rated_power_consumption', 'tab_key' => 'technical_specs', 'section_key' => 'parametry_elektryczne'],
                ['field_name' => 'rated_voltage', 'tab_key' => 'technical_specs', 'section_key' => 'parametry_elektryczne'],
                // technical_specs tab - wymiary_i_waga section
                ['field_name' => 'width', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                ['field_name' => 'height', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                ['field_name' => 'depth', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                ['field_name' => 'weight', 'tab_key' => 'technical_specs', 'section_key' => 'wymiary_i_waga'],
                // additional_info tab - galeria_i_dokumentacja section
                ['field_name' => 'gallery', 'tab_key' => 'additional_info', 'section_key' => 'galeria_i_dokumentacja'],
                ['field_name' => 'manual_file', 'tab_key' => 'additional_info', 'section_key' => 'galeria_i_dokumentacja'],
                // additional_info tab - dane_systemowe section
                ['field_name' => 'remote_id', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'sort', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'user_created', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'date_created', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'user_updated', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
                ['field_name' => 'date_updated', 'tab_key' => 'additional_info', 'section_key' => 'dane_systemowe'],
            ],
            'sensors' => [
                // basic_info tab - podstawowe_informacje section
                ['field_name' => 'status', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'model', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'brand_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price_before', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'image', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'discount_info', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'partner_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                // basic_info tab - linki_partnerskie section
                ['field_name' => 'partner_link_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                // basic_info tab - ceneo section
                ['field_name' => 'ceneo_url', 'tab_key' => 'basic_info', 'section_key' => 'ceneo'],
                ['field_name' => 'ceneo_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'ceneo'],
                ['field_name' => 'ceneo_link_title', 'tab_key' => 'basic_info', 'section_key' => 'ceneo'],
                // basic_info tab - link_do_recenzji section
                ['field_name' => 'review_link', 'tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji'],
                // pm_sensors tab - pm1_sensor section
                ['field_name' => 'is_pm1', 'tab_key' => 'pm_sensors', 'section_key' => 'pm1_sensor'],
                ['field_name' => 'pm1_range', 'tab_key' => 'pm_sensors', 'section_key' => 'pm1_sensor'],
                ['field_name' => 'pm1_accuracy', 'tab_key' => 'pm_sensors', 'section_key' => 'pm1_sensor'],
                ['field_name' => 'pm1_sensor_type', 'tab_key' => 'pm_sensors', 'section_key' => 'pm1_sensor'],
                // pm_sensors tab - pm2_sensor section
                ['field_name' => 'is_pm2', 'tab_key' => 'pm_sensors', 'section_key' => 'pm2_sensor'],
                ['field_name' => 'pm2_range', 'tab_key' => 'pm_sensors', 'section_key' => 'pm2_sensor'],
                ['field_name' => 'pm2_accuracy', 'tab_key' => 'pm_sensors', 'section_key' => 'pm2_sensor'],
                ['field_name' => 'pm2_sensor_type', 'tab_key' => 'pm_sensors', 'section_key' => 'pm2_sensor'],
                // pm_sensors tab - pm10_sensor section
                ['field_name' => 'is_pm10', 'tab_key' => 'pm_sensors', 'section_key' => 'pm10_sensor'],
                ['field_name' => 'pm10_range', 'tab_key' => 'pm_sensors', 'section_key' => 'pm10_sensor'],
                ['field_name' => 'pm10_accuracy', 'tab_key' => 'pm_sensors', 'section_key' => 'pm10_sensor'],
                ['field_name' => 'pm10_sensor_type', 'tab_key' => 'pm_sensors', 'section_key' => 'pm10_sensor'],
                // chemical_sensors tab - lzo_sensor section
                ['field_name' => 'is_lzo', 'tab_key' => 'chemical_sensors', 'section_key' => 'lzo_sensor'],
                ['field_name' => 'lzo_range', 'tab_key' => 'chemical_sensors', 'section_key' => 'lzo_sensor'],
                ['field_name' => 'lzo_accuracy', 'tab_key' => 'chemical_sensors', 'section_key' => 'lzo_sensor'],
                ['field_name' => 'lzo_sensor_type', 'tab_key' => 'chemical_sensors', 'section_key' => 'lzo_sensor'],
                // chemical_sensors tab - hcho_sensor section
                ['field_name' => 'is_hcho', 'tab_key' => 'chemical_sensors', 'section_key' => 'hcho_sensor'],
                ['field_name' => 'hcho_range', 'tab_key' => 'chemical_sensors', 'section_key' => 'hcho_sensor'],
                ['field_name' => 'hcho_accuracy', 'tab_key' => 'chemical_sensors', 'section_key' => 'hcho_sensor'],
                ['field_name' => 'hcho_sensor_type', 'tab_key' => 'chemical_sensors', 'section_key' => 'hcho_sensor'],
                // chemical_sensors tab - co2_sensor section
                ['field_name' => 'is_co2', 'tab_key' => 'chemical_sensors', 'section_key' => 'co2_sensor'],
                ['field_name' => 'co2_range', 'tab_key' => 'chemical_sensors', 'section_key' => 'co2_sensor'],
                ['field_name' => 'co2_accuracy', 'tab_key' => 'chemical_sensors', 'section_key' => 'co2_sensor'],
                ['field_name' => 'co2_sensor_type', 'tab_key' => 'chemical_sensors', 'section_key' => 'co2_sensor'],
                // chemical_sensors tab - co_sensor section
                ['field_name' => 'is_co', 'tab_key' => 'chemical_sensors', 'section_key' => 'co_sensor'],
                ['field_name' => 'co_range', 'tab_key' => 'chemical_sensors', 'section_key' => 'co_sensor'],
                ['field_name' => 'co_accuracy', 'tab_key' => 'chemical_sensors', 'section_key' => 'co_sensor'],
                ['field_name' => 'co_sensor_type', 'tab_key' => 'chemical_sensors', 'section_key' => 'co_sensor'],
                // environmental_sensors tab - temperature_sensor section
                ['field_name' => 'is_temperature', 'tab_key' => 'environmental_sensors', 'section_key' => 'temperature_sensor'],
                ['field_name' => 'temperature_range', 'tab_key' => 'environmental_sensors', 'section_key' => 'temperature_sensor'],
                ['field_name' => 'temperature_accuracy', 'tab_key' => 'environmental_sensors', 'section_key' => 'temperature_sensor'],
                ['field_name' => 'temperature', 'tab_key' => 'environmental_sensors', 'section_key' => 'temperature_sensor'],
                // environmental_sensors tab - humidity_sensor section
                ['field_name' => 'is_humidity', 'tab_key' => 'environmental_sensors', 'section_key' => 'humidity_sensor'],
                ['field_name' => 'humidity_range', 'tab_key' => 'environmental_sensors', 'section_key' => 'humidity_sensor'],
                ['field_name' => 'humidity_accuracy', 'tab_key' => 'environmental_sensors', 'section_key' => 'humidity_sensor'],
                ['field_name' => 'humidity', 'tab_key' => 'environmental_sensors', 'section_key' => 'humidity_sensor'],
                // environmental_sensors tab - pressure_sensor section
                ['field_name' => 'is_pressure', 'tab_key' => 'environmental_sensors', 'section_key' => 'pressure_sensor'],
                ['field_name' => 'pressure_range', 'tab_key' => 'environmental_sensors', 'section_key' => 'pressure_sensor'],
                ['field_name' => 'pressure_accuracy', 'tab_key' => 'environmental_sensors', 'section_key' => 'pressure_sensor'],
                // power_and_connectivity tab - power section
                ['field_name' => 'battery', 'tab_key' => 'power_and_connectivity', 'section_key' => 'power'],
                ['field_name' => 'battery_capacity', 'tab_key' => 'power_and_connectivity', 'section_key' => 'power'],
                ['field_name' => 'voltage', 'tab_key' => 'power_and_connectivity', 'section_key' => 'power'],
                ['field_name' => 'has_power_cord', 'tab_key' => 'power_and_connectivity', 'section_key' => 'power'],
                // power_and_connectivity tab - connectivity section
                ['field_name' => 'wifi', 'tab_key' => 'power_and_connectivity', 'section_key' => 'connectivity'],
                ['field_name' => 'bluetooth', 'tab_key' => 'power_and_connectivity', 'section_key' => 'connectivity'],
                ['field_name' => 'mobile_features', 'tab_key' => 'power_and_connectivity', 'section_key' => 'connectivity'],
                // device_features tab - features section
                ['field_name' => 'has_history', 'tab_key' => 'device_features', 'section_key' => 'features'],
                ['field_name' => 'has_display', 'tab_key' => 'device_features', 'section_key' => 'features'],
                ['field_name' => 'has_alarm', 'tab_key' => 'device_features', 'section_key' => 'features'],
                ['field_name' => 'has_assessment', 'tab_key' => 'device_features', 'section_key' => 'features'],
                ['field_name' => 'has_outdoor_indicator', 'tab_key' => 'device_features', 'section_key' => 'features'],
                ['field_name' => 'has_battery_indicator', 'tab_key' => 'device_features', 'section_key' => 'features'],
                ['field_name' => 'has_clock', 'tab_key' => 'device_features', 'section_key' => 'features'],
                // dimensions_and_performance tab - physical_dimensions section
                ['field_name' => 'width', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'physical_dimensions'],
                ['field_name' => 'height', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'physical_dimensions'],
                ['field_name' => 'depth', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'physical_dimensions'],
                ['field_name' => 'weight', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'physical_dimensions'],
                // dimensions_and_performance tab - performance_rating section
                ['field_name' => 'capability_points', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'performance_rating'],
                ['field_name' => 'capability', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'performance_rating'],
                ['field_name' => 'profitability_points', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'performance_rating'],
                ['field_name' => 'profitability', 'tab_key' => 'dimensions_and_performance', 'section_key' => 'performance_rating'],
                // ranking tab - ranking_settings section
                ['field_name' => 'ranking', 'tab_key' => 'ranking', 'section_key' => 'ranking_settings'],
                ['field_name' => 'ranking_hidden', 'tab_key' => 'ranking', 'section_key' => 'ranking_settings'],
                ['field_name' => 'main_ranking', 'tab_key' => 'ranking', 'section_key' => 'ranking_settings'],
                // metadata tab - system_identifiers section
                ['field_name' => 'remote_id', 'tab_key' => 'metadata', 'section_key' => 'system_identifiers'],
                ['field_name' => 'sort', 'tab_key' => 'metadata', 'section_key' => 'system_identifiers'],
                // metadata tab - timestamps section
                ['field_name' => 'user_created', 'tab_key' => 'metadata', 'section_key' => 'timestamps'],
                ['field_name' => 'user_updated', 'tab_key' => 'metadata', 'section_key' => 'timestamps'],
                ['field_name' => 'date_created', 'tab_key' => 'metadata', 'section_key' => 'timestamps'],
                ['field_name' => 'date_updated', 'tab_key' => 'metadata', 'section_key' => 'timestamps'],
            ],
            'upright_vacuums' => [
                // basic_info tab - podstawowe_informacje section
                ['field_name' => 'status', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'model', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'brand_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'type', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'price_before', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'image', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'discount_info', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                ['field_name' => 'partner_name', 'tab_key' => 'basic_info', 'section_key' => 'podstawowe_informacje'],
                // basic_info tab - linki_partnerskie section
                ['field_name' => 'partner_link_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                ['field_name' => 'partner_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_partnerskie'],
                // basic_info tab - linki_ceneo section
                ['field_name' => 'ceneo_url', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                ['field_name' => 'ceneo_link_rel_2', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                ['field_name' => 'ceneo_link_title', 'tab_key' => 'basic_info', 'section_key' => 'linki_ceneo'],
                // basic_info tab - link_do_recenzji section
                ['field_name' => 'review_link', 'tab_key' => 'basic_info', 'section_key' => 'link_do_recenzji'],
                // power_and_performance tab - parametry_ssania section
                ['field_name' => 'vacuum_cleaner_type', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'suction_power_aw', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'suction_power_pa', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'number_of_suction_power_levels', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'automatic_power_adjustment', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'suction_power_highest_level_pa', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'suction_power_medium_level_pa', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                ['field_name' => 'suction_power_low_level_pa', 'tab_key' => 'power_and_performance', 'section_key' => 'parametry_ssania'],
                // power_and_performance tab - silnik section
                ['field_name' => 'maximum_engine_power', 'tab_key' => 'power_and_performance', 'section_key' => 'silnik'],
                ['field_name' => 'rotation_speed', 'tab_key' => 'power_and_performance', 'section_key' => 'silnik'],
                ['field_name' => 'noise_level', 'tab_key' => 'power_and_performance', 'section_key' => 'silnik'],
                // power_and_battery tab - zasilanie section
                ['field_name' => 'power_supply', 'tab_key' => 'power_and_battery', 'section_key' => 'zasilanie'],
                ['field_name' => 'cable_length', 'tab_key' => 'power_and_battery', 'section_key' => 'zasilanie'],
                // power_and_battery tab - bateria section
                ['field_name' => 'battery_change', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'maximum_operation_time', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'battery_charging_time', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'battery_voltage', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'battery_capacity', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'operation_time_turbo', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'operation_time_eco', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                ['field_name' => 'displaying_battery_status', 'tab_key' => 'power_and_battery', 'section_key' => 'bateria'],
                // cleaning_functions tab - funkcje_mopowania section
                ['field_name' => 'mopping_function', 'tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania'],
                ['field_name' => 'active_washing_function', 'tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania'],
                ['field_name' => 'self_cleaning_function', 'tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania'],
                ['field_name' => 'self_cleaning_underlays', 'tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania'],
                ['field_name' => 'mopping_time_max', 'tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania'],
                ['field_name' => 'type_of_washing', 'tab_key' => 'cleaning_functions', 'section_key' => 'funkcje_mopowania'],
                // cleaning_functions tab - zbiorniki section
                ['field_name' => 'clean_water_tank_capacity', 'tab_key' => 'cleaning_functions', 'section_key' => 'zbiorniki'],
                ['field_name' => 'dirty_water_tank_capacity', 'tab_key' => 'cleaning_functions', 'section_key' => 'zbiorniki'],
                ['field_name' => 'dust_tank_capacity', 'tab_key' => 'cleaning_functions', 'section_key' => 'zbiorniki'],
                ['field_name' => 'easy_emptying_tank', 'tab_key' => 'cleaning_functions', 'section_key' => 'zbiorniki'],
                // filters_and_technologies tab - system_filtracji section
                ['field_name' => 'pollution_filtration_system', 'tab_key' => 'filters_and_technologies', 'section_key' => 'system_filtracji'],
                ['field_name' => 'cyclone_technology', 'tab_key' => 'filters_and_technologies', 'section_key' => 'system_filtracji'],
                ['field_name' => 'mesh_filter', 'tab_key' => 'filters_and_technologies', 'section_key' => 'system_filtracji'],
                ['field_name' => 'hepa_filter', 'tab_key' => 'filters_and_technologies', 'section_key' => 'system_filtracji'],
                ['field_name' => 'epa_filter', 'tab_key' => 'filters_and_technologies', 'section_key' => 'system_filtracji'],
                // filters_and_technologies tab - dodatkowe_technologie section
                ['field_name' => 'uv_technology', 'tab_key' => 'filters_and_technologies', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'led_backlight', 'tab_key' => 'filters_and_technologies', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'detecting_dirt_on_the_floor', 'tab_key' => 'filters_and_technologies', 'section_key' => 'dodatkowe_technologie'],
                ['field_name' => 'detecting_carpet', 'tab_key' => 'filters_and_technologies', 'section_key' => 'dodatkowe_technologie'],
                // brushes_and_accessories tab - szczotki section
                ['field_name' => 'electric_brush', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'turbo_brush', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'carpet_and_floor_brush', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'attachment_for_pets', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'bendable_pipe', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'telescopic_tube', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'hand_vacuum_cleaner', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                ['field_name' => 'charging_station', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'szczotki'],
                // brushes_and_accessories tab - wyposazenie_dodatkowe section
                ['field_name' => 'additional_equipment', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'wyposazenie_dodatkowe'],
                ['field_name' => 'continuous_work', 'tab_key' => 'brushes_and_accessories', 'section_key' => 'wyposazenie_dodatkowe'],
                // display_and_controls tab - wyswietlacz section
                ['field_name' => 'display', 'tab_key' => 'display_and_controls', 'section_key' => 'wyswietlacz'],
                ['field_name' => 'display_type', 'tab_key' => 'display_and_controls', 'section_key' => 'wyswietlacz'],
                // display_and_controls tab - czas_pracy section
                ['field_name' => 'vacuuming_time_max', 'tab_key' => 'display_and_controls', 'section_key' => 'czas_pracy'],
                ['field_name' => 'warranty', 'tab_key' => 'display_and_controls', 'section_key' => 'czas_pracy'],
                // additional_info tab - wyglad_i_wymiary section
                ['field_name' => 'colors', 'tab_key' => 'additional_info', 'section_key' => 'wyglad_i_wymiary'],
                ['field_name' => 'weight', 'tab_key' => 'additional_info', 'section_key' => 'wyglad_i_wymiary'],
                ['field_name' => 'weight_hand', 'tab_key' => 'additional_info', 'section_key' => 'wyglad_i_wymiary'],
                // additional_info tab - przeznaczenie section
                ['field_name' => 'for_pet_owners', 'tab_key' => 'additional_info', 'section_key' => 'przeznaczenie'],
                ['field_name' => 'for_allergy_sufferers', 'tab_key' => 'additional_info', 'section_key' => 'przeznaczenie'],
                // additional_info tab - oceny_i_ranking section
                ['field_name' => 'capability', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'profitability', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'ranking', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'ranking_hidden', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'main_ranking', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
                ['field_name' => 'videorecenzja1', 'tab_key' => 'additional_info', 'section_key' => 'oceny_i_ranking'],
            ],
        ];
    }
}
