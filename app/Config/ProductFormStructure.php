<?php

declare(strict_types=1);

namespace App\Config;

final class ProductFormStructure
{
    /**
     * SQL reserved words that cannot be used as column names.
     *
     * @var list<string>
     */
    public const RESERVED_COLUMN_NAMES = [
        'select', 'from', 'where', 'order', 'group', 'by', 'insert', 'update',
        'delete', 'drop', 'create', 'alter', 'table', 'index', 'into', 'values',
        'set', 'join', 'on', 'and', 'or', 'not', 'null', 'is', 'in', 'like',
        'between', 'having', 'limit', 'offset', 'union', 'all', 'as', 'distinct',
        'exists', 'case', 'when', 'then', 'else', 'end', 'asc', 'desc', 'key',
        'primary', 'foreign', 'references', 'constraint', 'default', 'check',
        'unique', 'column', 'database', 'schema', 'grant', 'revoke', 'trigger',
        'view', 'procedure', 'function', 'return', 'declare', 'cursor', 'fetch',
        'open', 'close', 'begin', 'commit', 'rollback', 'savepoint', 'true', 'false',
    ];

    public const MAX_LABEL_LENGTH = 255;

    /**
     * @param  string  $tableName
     * @return array<string, array<string, list<string>>>
     */
    public static function getMap(string $tableName): array
    {
        return self::MAPS[$tableName] ?? [];
    }

    /**
     * @return list<string>
     */
    public static function supportedTables(): array
    {
        return array_keys(self::MAPS);
    }

    public static function isReservedColumnName(string $name): bool
    {
        return in_array(strtolower($name), self::RESERVED_COLUMN_NAMES, true);
    }

    /**
     * @var array<string, array<string, array<string, list<string>>>>
     */
    private const MAPS = [
        'air_purifiers' => [
            'Podstawowe informacje' => [
                'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                'Oceny i ranking'       => ['capability_points', 'profitability_points', 'popularity'],
                'Linki partnerskie'     => ['partner_link_url', 'partner_link_rel_2'],
                'Ceneo'                 => ['ceneo_url', 'ceneo_link_rel_2'],
                'Galeria'               => ['local_gallery'],
            ],
            'Wydajność' => [
                'Wydajność' => ['max_performance', 'max_area', 'max_area_ro', 'number_of_fan_speeds', 'min_loudness', 'max_loudness', 'min_rated_power_consumption', 'max_rated_power_consumption'],
            ],
            'Nawilżanie' => [
                'Nawilżanie' => ['has_humidification', 'humidification_type', 'humidification_switch', 'humidification_area', 'water_tank_capacity', 'humidification_efficiency'],
                'Higrostat'  => ['hygrometer', 'hygrostat', 'hygrostat_min', 'hygrostat_max'],
            ],
            'Filtry' => [
                'Filtr ewaporacyjny' => ['evaporative_filter', 'evaporative_filter_life', 'evaporative_filter_price'],
                'Filtr HEPA'         => ['hepa_filter', 'hepa_filter_class', 'effectiveness_hepa_filter', 'hepa_filter_service_life', 'hepa_filter_price'],
                'Filtr węglowy'      => ['carbon_filter', 'carbon_filter_service_life', 'carbon_filter_price'],
            ],
            'Funkcje' => [
                'Jonizator'    => ['ionization', 'ionizer_type', 'ionizer_switch'],
                'Inne funkcje' => ['uvc', 'mobile_app', 'remote_control', 'heating_and_cooling_function', 'cooling_function'],
                'Czujniki'     => ['pm2_sensor', 'lzo_tvcop_sensor', 'temperature_sensor', 'humidity_sensor', 'light_sensor'],
            ],
            'Wymiary' => [
                'Wymiary' => ['width', 'height', 'depth', 'weight', 'colors'],
            ],
            'Klasyfikacja' => [
                'Klasyfikacja' => ['type_of_device', 'main_ranking', 'ranking_hidden', 'for_kids', 'bedroom', 'smokers', 'office', 'kindergarten', 'astmatic', 'alergic'],
            ],
        ],
        'air_humidifiers' => [
            'Podstawowe informacje' => [
                'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                'Ranking'               => ['capability_points', 'profitability_points', 'popularity'],
                'Linki partnerskie'     => ['partner_link_url', 'partner_link_rel_2'],
                'Linki Ceneo'           => ['ceneo_url', 'ceneo_link_rel_2'],
                'Galeria'               => ['local_gallery'],
            ],
            'Wydajność' => [
                'Wydajność'         => ['max_humidification_efficiency', 'max_area', 'humidification_type'],
                'Głośność wentylatora' => ['min_fan_volume', 'max_fan_volume'],
                'Pobór mocy'        => ['min_rated_power_consumption', 'max_rated_power_consumption'],
            ],
            'Zbiornik na wodę' => [
                'Zbiornik na wodę' => ['water_tank_capacity', 'tank_fill_type'],
            ],
            'Sterowanie' => [
                'Funkcje smart' => ['mobile_app', 'wifi_24ghz', 'wifi_5ghz', 'display', 'remote_control'],
            ],
            'Filtry' => [
                'Filtr ewaporacyjny' => ['evaporative_filter', 'evaporative_filter_life', 'evaporative_filter_cost'],
                'Filtr węglowy'      => ['carbon_filter', 'carbon_filter_cost', 'carbon_filter_life'],
            ],
            'Wymiary' => [
                'Wymiary' => ['width', 'height', 'depth', 'weight', 'colors'],
            ],
        ],
        'air_conditioners' => [
            'Podstawowe informacje' => [
                'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                'Oceny i ranking'       => ['capability_points', 'profitability_points', 'popularity'],
                'Linki partnerskie'     => ['partner_link_url', 'partner_link_rel_2'],
                'Linki Ceneo'           => ['ceneo_url', 'ceneo_link_rel_2'],
                'Galeria'               => ['local_gallery'],
            ],
            'Wydajność chłodzenia' => [
                'Parametry chłodzenia' => ['max_cooling_power', 'min_cooling_power', 'max_area_cooling'],
            ],
            'Wydajność grzania' => [
                'Parametry grzania' => ['max_heating_power', 'min_heating_power'],
            ],
            'Tryby pracy i funkcje' => [
                'Tryby pracy' => ['swing_function'],
            ],
            'Specyfikacja techniczna' => [
                'Chłodziwo'     => ['refrigerant_type', 'refrigerant_amount', 'needs_refill'],
                'Wymiary i waga' => ['width', 'height', 'depth', 'weight'],
            ],
        ],
        'dehumidifiers' => [
            'Podstawowe informacje' => [
                'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                'Oceny i ranking'       => ['capability_points', 'profitability_points', 'popularity'],
                'Linki partnerskie'     => ['partner_link_url', 'partner_link_rel_2'],
                'Linki Ceneo'           => ['ceneo_url', 'ceneo_link_rel_2'],
                'Galeria'               => ['local_gallery'],
            ],
            'Wydajność osuszania' => [
                'Parametry osuszania' => ['max_dehumidification_efficiency', 'max_area'],
            ],
            'Specyfikacja techniczna' => [
                'Chłodziwo'     => ['refrigerant_type', 'refrigerant_amount', 'needs_refill'],
                'Wymiary i waga' => ['width', 'height', 'depth', 'weight'],
            ],
        ],
        'upright_vacuums' => [
            'Podstawowe informacje' => [
                'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                'Oceny i ranking'       => ['capability_points', 'profitability_points', 'popularity'],
                'Linki partnerskie'     => ['partner_link_url', 'partner_link_rel_2'],
                'Linki Ceneo'           => ['ceneo_url', 'ceneo_link_rel_2'],
                'Galeria'               => ['local_gallery'],
            ],
            'Moc i wydajność' => [
                'Parametry ssania' => ['suction_power', 'motor_type'],
            ],
            'Zasilanie i bateria' => [
                'Zasilanie' => ['power_supply', 'cable_length'],
                'Bateria'   => ['battery_capacity', 'charging_time'],
            ],
        ],
        'sensors' => [
            'Podstawowe informacje' => [
                'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                'Linki partnerskie'     => ['partner_link_url', 'partner_link_rel_2'],
                'Ceneo'                 => ['ceneo_url', 'ceneo_link_rel_2'],
            ],
            'Czujniki PM' => [
                'Czujnik PM1'   => ['pm1', 'pm1_accuracy'],
                'Czujnik PM2.5' => ['pm25', 'pm25_accuracy'],
                'Czujnik PM10'  => ['pm10', 'pm10_accuracy'],
            ],
        ],
    ];
}
