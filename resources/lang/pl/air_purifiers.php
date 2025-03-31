<?php

return [
    'remote_id' => 'Zdalny identyfikator',
    'status' => 'Status',
    'date_created' => 'Data utworzenia',
    'date_updated' => 'Data aktualizacji',
    'model' => 'Model',
    'brand_name' => 'Nazwa marki',
    'price' => 'Cena',
    'partner_link_url' => 'Link partnerski URL',
    'partner_link_rel_2' => 'Dodatkowy link partnerski',
    'ceneo_url' => 'URL Ceneo',
    'ceneo_link_rel_2' => 'Dodatkowy link Ceneo',
    'max_performance' => 'Maksymalna wydajność',
    'max_area' => 'Maksymalna powierzchnia',
    'max_area_ro' => 'Maksymalna powierzchnia RO',
    'has_humidification' => 'Posiada nawilżanie',
    'humidification_type' => 'Typ nawilżania',
    'humidification_switch' => 'Przełącznik nawilżania',
    'humidification_efficiency' => 'Wydajność nawilżania',
    'humidification_area' => 'Powierzchnia nawilżania',
    'water_tank_capacity' => 'Pojemność zbiornika wody',
    'hygrometer' => 'Higrometr',
    'hygrostat' => 'Higrostat',
    'evaporative_filter' => 'Filtr odparowujący',
    'evaporative_filter_life' => 'Żywotność filtra odparowującego',
    'evaporative_filter_price' => 'Cena filtra odparowującego',
    'ionizer_type' => 'Typ jonizatora',
    'ionizer_switch' => 'Przełącznik jonizatora',
    'mesh_filter' => 'Filtr siatkowy',
    'hepa_filter' => 'Filtr HEPA',
    'hepa_filter_service_life' => 'Żywotność filtra HEPA',
    'hepa_filter_price' => 'Cena filtra HEPA',
    'carbon_filter' => 'Filtr węglowy',
    'carbon_filter_service_life' => 'Żywotność filtra węglowego',
    'carbon_filter_price' => 'Cena filtra węglowego',
    'uvc' => 'Lampa UV-C',
    'mobile_app' => 'Aplikacja mobilna',
    'remote_control' => 'Pilot zdalnego sterowania',
    'width' => 'Szerokość',
    'height' => 'Wysokość',
    'weight' => 'Waga',
    'depth' => 'Głębokość',
    'review_link' => 'Link do recenzji',
    'ionization' => 'Jonizacja',
    'capability_points' => 'Punkty możliwości',
    'profitability_points' => 'Punkty opłacalności',
    'min_loudness' => 'Minimalna głośność',
    'max_loudness' => 'Maksymalna głośność',
    'max_rated_power_consumption' => 'Maksymalne zużycie energii',
    'certificates' => 'Certyfikaty',
    'pm2_sensor' => 'Czujnik PM2.5',
    'colors' => 'Kolory',
    'functions' => 'Funkcje',
    'lzo_tvcop_sensor' => 'Czujnik LZO/TVCOP',
    'temperature_sensor' => 'Czujnik temperatury',
    'humidity_sensor' => 'Czujnik wilgotności',
    'light_sensor' => 'Czujnik światła',
    'hepa_filter_class' => 'Klasa filtra HEPA',
    'effectiveness_hepa_filter' => 'Skuteczność filtra HEPA',
    'price_date' => 'Data wprowadzenia ceny',
    'ranking_hidden' => 'Ranking ukryty',
    'filter_costs' => 'Koszty filtrów',
    'functions_and_equipment' => 'Funkcje i wyposażenie',
    'heating_and_cooling_function' => 'Funkcja grzania i chłodzenia',
    'main_ranking' => 'Główny ranking',
    'for_kids' => 'Dla dzieci',
    'cooling_function' => 'Funkcja chłodzenia',
    'bedroom' => 'Do sypialni',
    'smokers' => 'Dla palaczy',
    'office' => 'Do biura',
    'kindergarten' => 'Do przedszkola',
    'astmatic' => 'Dla osób z astmą',
    'alergic' => 'Dla alergików',
    'type_of_device' => 'Typ urządzenia',
    'type' => 'Typ',
    'is_promo' => 'Promocja',

    'tabs' => [
        'air_purifier_form' => 'Formularz Oczyszczacza Powietrza',
        'basic_information' => 'Informacje Podstawowe',
        'performance' => 'Wydajność',
        'humidification' => 'Nawilżanie',
        'filters' => 'Filtry',
        'features' => 'Funkcje',
        'physical_attributes' => 'Parametry Fizyczne',
        'classification' => 'Klasyfikacja',
        'timestamps' => 'Znaczniki Czasu',
        'custom_fields' => 'Pola Niestandardowe',
    ],

    'fields' => [
        // Basic Information
        'status' => [
            'label' => 'Status',
            'options' => [
                'draft' => 'Wersja robocza',
                'pending' => 'Oczekujące',
                'published' => 'Opublikowane',
            ]
        ],
        'model' => 'Model',
        'brand_name' => 'Nazwa marki',
        'price' => 'Cena (PLN)',
        'price_date' => 'Data wprowadzenia ceny',
        'is_promo' => 'Produkt promocyjny',

        // Links Section
        'links' => [
            'section_title' => 'Linki',
            'partner_link_url' => 'Link partnerski URL',
            'partner_link_rel_2' => [
                'label' => 'Relacje linku partnerskiego',
                'placeholder' => 'Dodaj relację',
                'helper_text' => 'np. nofollow, noopener',
            ],
            'ceneo_url' => 'URL Ceneo',
            'ceneo_link_rel_2' => [
                'label' => 'Relacje linku Ceneo',
                'placeholder' => 'Dodaj relację',
                'helper_text' => 'np. nofollow, noopener',
            ],
            'review_link' => 'Link do recenzji',
        ],

        // Performance
        'max_performance' => 'Maksymalna wydajność (m³/h)',
        'max_area' => 'Maksymalna powierzchnia (m²)',
        'max_area_ro' => 'Maksymalna powierzchnia RO (m²)',
        'min_loudness' => 'Minimalna głośność (dB)',
        'max_loudness' => 'Maksymalna głośność (dB)',
        'max_rated_power_consumption' => 'Maksymalne zużycie energii (W)',
        'capability_points' => 'Punkty możliwości',
        'profitability_points' => 'Punkty opłacalności',

        // Humidification
        'has_humidification' => 'Posiada nawilżanie',
        'humidification_type' => [
            'label' => 'Typ nawilżania',
            'options' => [
                'vapor' => 'Parowy',
                'ultrasonic' => 'Ultradźwiękowy',
                'evaporative' => 'Odparowujący',
            ]
        ],
        'humidification_switch' => 'Przełącznik nawilżania',
        'humidification_efficiency' => 'Wydajność nawilżania (ml/h)',
        'humidification_area' => 'Powierzchnia nawilżania (m²)',
        'water_tank_capacity' => 'Pojemność zbiornika wody (ml)',
        'hygrometer' => 'Posiada higrometr',
        'hygrostat' => 'Posiada higrostat',

        // Filters
        'filters' => [
            'evaporative' => [
                'section_title' => 'Filtr odparowujący',
                'has_filter' => 'Posiada filtr odparowujący',
                'filter_life' => 'Żywotność filtra (miesiące)',
                'filter_price' => 'Cena filtra (PLN)',
            ],
            'hepa' => [
                'section_title' => 'Filtr HEPA',
                'has_filter' => 'Posiada filtr HEPA',
                'filter_class' => 'Klasa filtra HEPA',
                'filter_effectiveness' => 'Skuteczność filtra HEPA (%)',
                'filter_life' => 'Żywotność filtra (miesiące)',
                'filter_price' => 'Cena filtra (PLN)',
            ],
            'carbon' => [
                'section_title' => 'Filtr węglowy',
                'has_filter' => 'Posiada filtr węglowy',
                'filter_life' => 'Żywotność filtra (miesiące)',
                'filter_price' => 'Cena filtra (PLN)',
            ],
            'mesh_filter' => 'Posiada filtr siatkowy',
            'filter_costs' => 'Podsumowanie kosztów filtrów',
        ],

        // Features
        'features' => [
            'ionizer' => [
                'section_title' => 'Jonizator',
                'has_ionization' => 'Posiada jonizację',
                'ionizer_type' => 'Typ jonizatora',
                'ionizer_switch' => 'Przełącznik jonizatora',
            ],
            'other_features' => [
                'section_title' => 'Inne funkcje',
                'uvc' => 'Posiada lampę UV-C',
                'mobile_app' => 'Posiada aplikację mobilną',
                'remote_control' => 'Posiada pilot',
                'functions_and_equipment' => [
                    'label' => 'Funkcje i wyposażenie',
                    'placeholder' => 'Dodaj funkcję',
                ],
                'heating_and_cooling_function' => 'Funkcja grzania i chłodzenia',
                'cooling_function' => 'Funkcja chłodzenia',
            ],
            'sensors' => [
                'section_title' => 'Czujniki',
                'pm2_sensor' => 'Czujnik PM2.5',
                'lzo_tvcop_sensor' => 'Czujnik LZO/TVCOP',
                'temperature_sensor' => 'Czujnik temperatury',
                'humidity_sensor' => 'Czujnik wilgotności',
                'light_sensor' => 'Czujnik światła',
            ],
            'certificates' => [
                'label' => 'Certyfikaty',
                'placeholder' => 'Dodaj certyfikat',
            ],
        ],

        // Physical Attributes
        'physical_attributes' => [
            'width' => 'Szerokość (cm)',
            'height' => 'Wysokość (cm)',
            'depth' => 'Głębokość (cm)',
            'weight' => 'Waga (kg)',
            'colors' => [
                'label' => 'Kolory',
                'placeholder' => 'Dodaj kolor',
            ],
        ],

        // Classification
        'classification' => [
            'type_of_device' => [
                'label' => 'Typ urządzenia',
                'placeholder' => 'Dodaj typ urządzenia',
            ],
            'type' => 'Typ',
            'main_ranking' => 'Uwzględnij w głównym rankingu',
            'ranking_hidden' => 'Ukryj w rankingach',
            'suitability' => [
                'for_kids' => 'Odpowiedni dla dzieci',
                'bedroom' => 'Odpowiedni do sypialni',
                'smokers' => 'Odpowiedni dla palaczy',
                'office' => 'Odpowiedni do biura',
                'kindergarten' => 'Odpowiedni do przedszkola',
                'astmatic' => 'Odpowiedni dla osób z astmą',
                'alergic' => 'Odpowiedni dla alergików',
            ],
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
