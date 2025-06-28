<?php

return [
    // Basic Information
    'remote_id' => 'Zdalny identyfikator',
    'status' => 'Status',
    'sort' => 'Kolejność sortowania',
    'user_created' => 'Utworzone przez',
    'date_created' => 'Data utworzenia',
    'user_updated' => 'Zaktualizowane przez',
    'date_updated' => 'Data aktualizacji',
    'model' => 'Model',
    'brand_name' => 'Marka',
    'price' => 'Cena',
    'price_before' => 'Cena przed',
    'image' => 'Zdjęcie',
    'discount_info' => 'Informacje o zniżce',

    // Partner Information
    'partner_name' => 'Nazwa partnera',
    'partner_link_url' => 'Link partnerski',
    'partner_link_rel_2' => 'Atrybuty rel partnera',
    'partner_link_title' => 'Tytuł linku partnera',
    'ceneo_url' => 'Link Ceneo',
    'ceneo_link_rel_2' => 'Atrybuty rel Ceneo',
    'ceneo_link_title' => 'Tytuł linku Ceneo',
    'review_link' => 'Link do recenzji',

    // PM1 Sensor
    'is_pm1' => 'Posiada czujnik PM1',
    'pm1_range' => 'Zakres PM1',
    'pm1_accuracy' => 'Dokładność PM1',
    'pm1_sensor_type' => 'Typ czujnika PM1',

    // PM2.5 Sensor
    'is_pm2' => 'Posiada czujnik PM2.5',
    'pm2_range' => 'Zakres PM2.5',
    'pm2_accuracy' => 'Dokładność PM2.5',
    'pm2_sensor_type' => 'Typ czujnika PM2.5',

    // PM10 Sensor
    'is_pm10' => 'Posiada czujnik PM10',
    'pm10_range' => 'Zakres PM10',
    'pm10_accuracy' => 'Dokładność PM10',
    'pm10_sensor_type' => 'Typ czujnika PM10',

    // LZO Sensor
    'is_lzo' => 'Posiada czujnik LZO',
    'lzo_range' => 'Zakres LZO',
    'lzo_accuracy' => 'Dokładność LZO',
    'lzo_sensor_type' => 'Typ czujnika LZO',

    // HCHO Sensor
    'is_hcho' => 'Posiada czujnik HCHO',
    'hcho_range' => 'Zakres HCHO',
    'hcho_accuracy' => 'Dokładność HCHO',
    'hcho_sensor_type' => 'Typ czujnika HCHO',

    // CO2 Sensor
    'is_co2' => 'Posiada czujnik CO2',
    'co2_range' => 'Zakres CO2',
    'co2_accuracy' => 'Dokładność CO2',
    'co2_sensor_type' => 'Typ czujnika CO2',

    // CO Sensor
    'is_co' => 'Posiada czujnik CO',
    'co_range' => 'Zakres CO',
    'co_accuracy' => 'Dokładność CO',
    'co_sensor_type' => 'Typ czujnika CO',

    // Temperature Sensor
    'is_temperature' => 'Posiada czujnik temperatury',
    'temperature_range' => 'Zakres temperatury',
    'temperature_accuracy' => 'Dokładność temperatury',
    'temperature' => 'Aktualna temperatura',

    // Humidity Sensor
    'is_humidity' => 'Posiada czujnik wilgotności',
    'humidity_range' => 'Zakres wilgotności',
    'humidity_accuracy' => 'Dokładność wilgotności',
    'humidity' => 'Aktualna wilgotność',

    // Pressure Sensor
    'is_pressure' => 'Posiada czujnik ciśnienia',
    'pressure_range' => 'Zakres ciśnienia',
    'pressure_accuracy' => 'Dokładność ciśnienia',

    // Power and Connectivity
    'battery' => 'Typ baterii',
    'battery_capacity' => 'Pojemność baterii (mAh)',
    'voltage' => 'Napięcie (V)',
    'has_power_cord' => 'Posiada przewód zasilający',
    'wifi' => 'Wi-Fi',
    'bluetooth' => 'Bluetooth',
    'mobile_features' => 'Funkcje mobilne',

    // Device Features
    'has_history' => 'Historia pomiarów',
    'has_display' => 'Wyświetlacz',
    'has_alarm' => 'Alarm',
    'has_assessment' => 'Ocena jakości',
    'has_outdoor_indicator' => 'Wskaźnik zewnętrzny',
    'has_battery_indicator' => 'Wskaźnik baterii',
    'has_clock' => 'Zegar',

    // Physical Dimensions
    'width' => 'Szerokość',
    'height' => 'Wysokość',
    'depth' => 'Głębokość',
    'weight' => 'Waga',

    // Performance Rating
    'capability_points' => 'Punkty możliwości',
    'capability' => 'Możliwości',
    'profitability_points' => 'Punkty opłacalności',
    'profitability' => 'Opłacalność',

    // Ranking
    'ranking' => 'Pozycja w rankingu',
    'ranking_hidden' => 'Ukryj w rankingu',
    'main_ranking' => 'Główny ranking',

    // Timestamps
    'created_at' => 'Rekord utworzony',
    'updated_at' => 'Rekord zaktualizowany',

    'tabs' => [
        'sensor_form' => 'Formularz Czujnika',
        'basic_information' => 'Podstawowe informacje',
        'partner_links' => 'Linki partnerskie',
        'pm_sensors' => 'Czujniki PM',
        'chemical_sensors' => 'Czujniki chemiczne',
        'environmental_sensors' => 'Czujniki środowiskowe',
        'power_connectivity' => 'Zasilanie i łączność',
        'device_features' => 'Funkcje urządzenia',
        'dimensions_performance' => 'Wymiary i wydajność',
        'ranking' => 'Ranking',
        'metadata' => 'Metadane',
    ],

    'sections' => [
        'basic_info' => 'Podstawowe informacje',
        'partner_links' => 'Linki partnerskie',
        'pm1_sensor' => 'Czujnik PM1',
        'pm2_sensor' => 'Czujnik PM2.5',
        'pm10_sensor' => 'Czujnik PM10',
        'lzo_sensor' => 'Czujnik LZO (Ozon)',
        'hcho_sensor' => 'Czujnik HCHO (Formaldehyd)',
        'co2_sensor' => 'Czujnik CO2',
        'co_sensor' => 'Czujnik CO',
        'temperature_sensor' => 'Czujnik temperatury',
        'humidity_sensor' => 'Czujnik wilgotności',
        'pressure_sensor' => 'Czujnik ciśnienia',
        'power' => 'Zasilanie',
        'connectivity' => 'Łączność',
        'features' => 'Funkcje',
        'physical_dimensions' => 'Wymiary fizyczne',
        'performance_rating' => 'Ocena wydajności',
        'ranking_settings' => 'Ustawienia rankingu',
        'system_identifiers' => 'Identyfikatory systemu',
        'timestamps' => 'Znaczniki czasu',
    ],

    'fields' => [
        // Status options
        'status' => [
            'label' => 'Status',
            'options' => [
                'draft' => 'Szkic',
                'published' => 'Opublikowany',
                'archived' => 'Zarchiwizowany',
            ]
        ],
        
        // Basic Information
        'model' => 'Model',
        'brand_name' => 'Marka',
        'price' => 'Cena (PLN)',
        'price_before' => 'Cena przed (PLN)',
        'image' => 'Zdjęcie',
        'discount_info' => 'Informacje o zniżce',

        // Partner Links
        'partner_name' => 'Nazwa partnera',
        'partner_link_url' => 'Link partnerski',
        'partner_link_rel_2' => [
            'label' => 'Atrybuty rel partnera',
            'options' => [
                'nofollow' => 'nofollow',
                'dofollow' => 'dofollow',
                'sponsored' => 'sponsored',
                'noopener' => 'noopener',
            ]
        ],
        'partner_link_title' => 'Tytuł linku partnera',
        'ceneo_url' => 'Link Ceneo',
        'ceneo_link_rel_2' => [
            'label' => 'Atrybuty rel Ceneo',
            'options' => [
                'nofollow' => 'nofollow',
                'dofollow' => 'dofollow',
                'sponsored' => 'sponsored',
                'noopener' => 'noopener',
            ]
        ],
        'ceneo_link_title' => 'Tytuł linku Ceneo',
        'review_link' => 'Link do recenzji',

        // Sensors
        'pm1' => [
            'has_sensor' => 'Posiada czujnik PM1',
            'range' => 'Zakres PM1',
            'accuracy' => 'Dokładność PM1',
            'sensor_type' => 'Typ czujnika PM1',
        ],
        'pm2' => [
            'has_sensor' => 'Posiada czujnik PM2.5',
            'range' => 'Zakres PM2.5',
            'accuracy' => 'Dokładność PM2.5',
            'sensor_type' => 'Typ czujnika PM2.5',
        ],
        'pm10' => [
            'has_sensor' => 'Posiada czujnik PM10',
            'range' => 'Zakres PM10',
            'accuracy' => 'Dokładność PM10',
            'sensor_type' => 'Typ czujnika PM10',
        ],
        'lzo' => [
            'has_sensor' => 'Posiada czujnik LZO',
            'range' => 'Zakres LZO',
            'accuracy' => 'Dokładność LZO',
            'sensor_type' => 'Typ czujnika LZO',
        ],
        'hcho' => [
            'has_sensor' => 'Posiada czujnik HCHO',
            'range' => 'Zakres HCHO',
            'accuracy' => 'Dokładność HCHO',
            'sensor_type' => 'Typ czujnika HCHO',
        ],
        'co2' => [
            'has_sensor' => 'Posiada czujnik CO2',
            'range' => 'Zakres CO2',
            'accuracy' => 'Dokładność CO2',
            'sensor_type' => 'Typ czujnika CO2',
        ],
        'co' => [
            'has_sensor' => 'Posiada czujnik CO',
            'range' => 'Zakres CO',
            'accuracy' => 'Dokładność CO',
            'sensor_type' => 'Typ czujnika CO',
        ],
        'temperature' => [
            'has_sensor' => 'Posiada czujnik temperatury',
            'range' => 'Zakres temperatury',
            'accuracy' => 'Dokładność temperatury',
            'current_value' => 'Aktualna temperatura',
        ],
        'humidity' => [
            'has_sensor' => 'Posiada czujnik wilgotności',
            'range' => 'Zakres wilgotności',
            'accuracy' => 'Dokładność wilgotności',
            'current_value' => 'Aktualna wilgotność',
        ],
        'pressure' => [
            'has_sensor' => 'Posiada czujnik ciśnienia',
            'range' => 'Zakres ciśnienia',
            'accuracy' => 'Dokładność ciśnienia',
        ],

        // Power and Connectivity
        'power' => [
            'battery' => 'Typ baterii',
            'battery_capacity' => 'Pojemność baterii (mAh)',
            'voltage' => 'Napięcie (V)',
            'has_power_cord' => 'Posiada przewód zasilający',
        ],
        'connectivity' => [
            'wifi' => 'Wi-Fi',
            'bluetooth' => 'Bluetooth',
            'mobile_features' => [
                'label' => 'Funkcje mobilne',
                'placeholder' => 'Dodaj funkcję mobilną',
            ],
        ],

        // Device Features
        'device_features' => [
            'has_history' => 'Historia pomiarów',
            'has_display' => 'Wyświetlacz',
            'has_alarm' => 'Alarm',
            'has_assessment' => 'Ocena jakości',
            'has_outdoor_indicator' => 'Wskaźnik zewnętrzny',
            'has_battery_indicator' => 'Wskaźnik baterii',
            'has_clock' => 'Zegar',
        ],

        // Physical Dimensions
        'physical_dimensions' => [
            'width' => 'Szerokość (cm)',
            'height' => 'Wysokość (cm)',
            'depth' => 'Głębokość (cm)',
            'weight' => 'Waga (kg)',
        ],

        // Performance Rating
        'performance_rating' => [
            'capability_points' => 'Punkty możliwości',
            'capability' => 'Możliwości',
            'profitability_points' => 'Punkty opłacalności',
            'profitability' => 'Opłacalność',
        ],

        // Ranking
        'ranking_settings' => [
            'ranking' => 'Pozycja w rankingu',
            'ranking_hidden' => 'Ukryj w rankingu',
            'main_ranking' => 'Główny ranking',
        ],

        // System Identifiers
        'system_identifiers' => [
            'remote_id' => 'ID zdalne',
            'sort' => 'Kolejność sortowania',
            'user_created' => 'Utworzone przez',
            'user_updated' => 'Zaktualizowane przez',
        ],

        // Timestamps
        'timestamps' => [
            'date_created' => 'Data utworzenia',
            'date_updated' => 'Data aktualizacji',
            'created_at' => 'Rekord utworzony',
            'updated_at' => 'Rekord zaktualizowany',
        ],
    ],
]; 