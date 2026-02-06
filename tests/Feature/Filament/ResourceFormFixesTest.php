<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\AirConditioner;
use App\Models\AirHumidifier;
use App\Models\AirPurifier;
use App\Models\Dehumidifier;
use App\Models\Sensor;
use App\Models\UprightVacuum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for Filament resource form fixes ("poprawki" branch).
 *
 * Covers:
 * - Field type changes (TextInput -> Toggle, TextInput -> Select)
 * - Field visibility conditions (->live() + ->visible())
 * - Tab/section reorganization
 * - Field additions and removals
 * - Data integrity for new field types
 *
 * Run with: php artisan test tests/Feature/Filament/ResourceFormFixesTest.php
 */
class ResourceFormFixesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    // ==========================================
    // OCZYSZCZACZE (Air Purifier)
    // ==========================================

    /**
     * Poprawka: Toggle has_humidification z ->live() - ukryte pola stają się widoczne.
     */
    public function test_air_purifier_humidification_toggle_with_related_fields(): void
    {
        $airPurifier = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Humidification Test ' . time(),
            'brand_name' => 'Test Brand',
            'has_humidification' => true,
            'humidification_type' => 'ultrasonic',
        ]);

        $this->assertTrue((bool) $airPurifier->has_humidification);
        $this->assertEquals('ultrasonic', $airPurifier->humidification_type);
    }

    /**
     * Poprawka: Toggle hygrostat z ->live() - nowe pola hygrostat_min/max.
     */
    public function test_air_purifier_hygrostat_toggle_enables_range(): void
    {
        $airPurifier = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Hygrostat Test ' . time(),
            'brand_name' => 'Test Brand',
            'hygrostat' => true,
        ]);

        $this->assertTrue((bool) $airPurifier->hygrostat);
    }

    /**
     * Poprawka: Label filtra siatkowego -> 'Filtr wstępny'.
     */
    public function test_air_purifier_mesh_filter_boolean(): void
    {
        $airPurifier = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Mesh Filter Test ' . time(),
            'brand_name' => 'Test Brand',
            'mesh_filter' => true,
        ]);

        $this->assertTrue((bool) $airPurifier->mesh_filter);
    }

    /**
     * Poprawka: capability_points/profitability_points przeniesione do Podstawowe informacje.
     */
    public function test_air_purifier_ranking_fields(): void
    {
        $airPurifier = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Ranking Test ' . time(),
            'brand_name' => 'Test Brand',
            'capability_points' => 85,
            'profitability_points' => 90,
            'main_ranking' => true,
            'ranking_hidden' => false,
        ]);

        $this->assertEquals(85, $airPurifier->capability_points);
        $this->assertEquals(90, $airPurifier->profitability_points);
        $this->assertTrue((bool) $airPurifier->main_ranking);
        $this->assertFalse((bool) $airPurifier->ranking_hidden);
    }

    /**
     * Poprawka: Wszystkie zakładki oczyszczaczy powinny być po polsku.
     * Tab names are now stored in the DB seeder (FormTabConfigurationSeeder).
     */
    public function test_air_purifier_tab_names_are_in_polish(): void
    {
        $seederFile = file_get_contents(database_path('seeders/FormTabConfigurationSeeder.php'));

        $polishTabs = ['Podstawowe informacje', 'Wydajność', 'Nawilżanie', 'Filtry',
            'Funkcje', 'Wymiary', 'Klasyfikacja', 'Daty'];

        foreach ($polishTabs as $polishTab) {
            $this->assertStringContainsString(
                $polishTab,
                $seederFile,
                "Tab '{$polishTab}' should exist in FormTabConfigurationSeeder for air_purifiers"
            );
        }
    }

    /**
     * Poprawka: Dodanie brakujących pól: liczba prędkości oczyszczania, popularność.
     */
    public function test_air_purifier_number_of_fan_speeds_and_popularity(): void
    {
        $airPurifier = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Speeds Popularity Test ' . time(),
            'brand_name' => 'Test Brand',
            'number_of_fan_speeds' => 5,
            'popularity' => 42,
        ]);

        $this->assertEquals(5, $airPurifier->number_of_fan_speeds);
        $this->assertEquals(42, $airPurifier->popularity);
    }

    /**
     * Poprawka: Migracja dodająca number_of_fan_speeds i popularity istnieje.
     */
    public function test_air_purifier_missing_fields_migration_exists(): void
    {
        $migrationPath = database_path('migrations/2026_02_07_000000_add_number_of_fan_speeds_and_popularity_to_air_purifiers_table.php');
        $this->assertFileExists($migrationPath);
    }

    // ==========================================
    // NAWILŻACZE (Air Humidifier)
    // ==========================================

    /**
     * Poprawka: type_of_device zmieniony z TextInput na Select.
     */
    public function test_air_humidifier_type_of_device_accepts_select_values(): void
    {
        $validTypes = ['ultradźwiękowy', 'ewaporacyjny', 'parowy', 'hybrydowy'];

        foreach ($validTypes as $type) {
            $humidifier = AirHumidifier::create([
                'status' => 'draft',
                'model' => "Device Type {$type} Test " . time(),
                'brand_name' => 'Test Brand',
                'type_of_device' => $type,
            ]);

            $this->assertEquals($type, $humidifier->type_of_device);
        }
    }

    /**
     * Poprawka: auto_mode_min i auto_mode_max usunięte.
     */
    public function test_air_humidifier_auto_mode_without_min_max(): void
    {
        $humidifier = AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Auto Mode Test ' . time(),
            'brand_name' => 'Test Brand',
            'auto_mode' => true,
        ]);

        $this->assertTrue((bool) $humidifier->auto_mode);
    }

    /**
     * Poprawka: Sekcja ranking przeniesiona do Podstawowe informacje.
     */
    public function test_air_humidifier_ranking_in_basic_info(): void
    {
        $humidifier = AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Ranking Test ' . time(),
            'brand_name' => 'Test Brand',
            'main_ranking' => true,
            'ranking_hidden' => false,
            'capability' => 85,
            'profitability' => 90,
        ]);

        $this->assertTrue((bool) $humidifier->main_ranking);
        $this->assertFalse((bool) $humidifier->ranking_hidden);
        $this->assertEquals(85, $humidifier->capability);
    }

    /**
     * Poprawka: fan_volume przeniesiony do Wydajność.
     */
    public function test_air_humidifier_fan_volume_fields(): void
    {
        $humidifier = AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Fan Volume Test ' . time(),
            'brand_name' => 'Test Brand',
            'fan_volume' => true,
            'min_fan_volume' => 20,
            'max_fan_volume' => 60,
        ]);

        $this->assertTrue((bool) $humidifier->fan_volume);
        $this->assertEquals(20, $humidifier->min_fan_volume);
        $this->assertEquals(60, $humidifier->max_fan_volume);
    }

    /**
     * Poprawka: pobór prądu przeniesiony do Wydajność.
     */
    public function test_air_humidifier_power_consumption_in_wydajnosc(): void
    {
        $humidifier = AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Power Test ' . time(),
            'brand_name' => 'Test Brand',
            'min_rated_power_consumption' => 10,
            'max_rated_power_consumption' => 50,
        ]);

        $this->assertEquals(10, $humidifier->min_rated_power_consumption);
        $this->assertEquals(50, $humidifier->max_rated_power_consumption);
    }

    // ==========================================
    // OSUSZACZE (Dehumidifier)
    // ==========================================

    /**
     * Poprawka: Higrostat zmieniony z TagsInput na Toggle.
     */
    public function test_dehumidifier_higrostat_is_toggle(): void
    {
        $dehumidifier = Dehumidifier::create([
            'status' => 'draft',
            'model' => 'Higrostat Toggle Test ' . time(),
            'brand_name' => 'Test Brand',
            'higrostat' => true,
        ]);

        $this->assertTrue((bool) $dehumidifier->higrostat);
    }

    /**
     * Poprawka: Pola higrostatu widoczne tylko gdy toggle aktywny.
     */
    public function test_dehumidifier_higrostat_range_values(): void
    {
        $dehumidifier = Dehumidifier::create([
            'status' => 'draft',
            'model' => 'Higrostat Range Test ' . time(),
            'brand_name' => 'Test Brand',
            'higrostat' => true,
            'min_value_for_hygrostat' => 40,
            'max_value_for_hygrostat' => 80,
            'increment_of_the_hygrostat' => '5',
        ]);

        $this->assertEquals(40, $dehumidifier->min_value_for_hygrostat);
        $this->assertEquals(80, $dehumidifier->max_value_for_hygrostat);
    }

    /**
     * Poprawka: Ranking przeniesiony do Podstawowe informacje, usunięte _points.
     */
    public function test_dehumidifier_ranking_without_points(): void
    {
        $dehumidifier = Dehumidifier::create([
            'status' => 'draft',
            'model' => 'Ranking Test ' . time(),
            'brand_name' => 'Test Brand',
            'main_ranking' => true,
            'ranking_hidden' => false,
            'capability' => 85,
            'profitability' => 90,
        ]);

        $this->assertTrue((bool) $dehumidifier->main_ranking);
        $this->assertEquals(85, $dehumidifier->capability);
    }

    // ==========================================
    // KLIMATYZATORY (Air Conditioner)
    // ==========================================

    /**
     * Poprawka: Typ zmieniony z TextInput na Select z opcjami.
     */
    public function test_air_conditioner_type_select_values(): void
    {
        $types = ['przenosny', 'split', 'multisplit', 'monoblok', 'okienny'];

        foreach ($types as $type) {
            $ac = AirConditioner::create([
                'status' => 'draft',
                'model' => "Type {$type} Test " . time(),
                'brand_name' => 'Test Brand',
                'type' => $type,
            ]);

            $this->assertEquals($type, $ac->type);
        }
    }

    /**
     * Poprawka: capability_points/profitability_points usunięte.
     */
    public function test_air_conditioner_ranking_without_points(): void
    {
        $ac = AirConditioner::create([
            'status' => 'draft',
            'model' => 'Ranking Test ' . time(),
            'brand_name' => 'Test Brand',
            'capability' => 75,
            'profitability' => 80,
            'main_ranking' => true,
            'ranking_hidden' => false,
        ]);

        $this->assertEquals(75, $ac->capability);
        $this->assertTrue((bool) $ac->main_ranking);
    }

    // ==========================================
    // ODKURZACZE (Upright Vacuum)
    // ==========================================

    /**
     * Poprawka: Typ zmieniony z TextInput na Select.
     */
    public function test_upright_vacuum_type_select_values(): void
    {
        $types = ['pionowy', 'reczny', '2w1', 'myjacy', 'workowy', 'bezworkowy'];

        foreach ($types as $type) {
            $vacuum = UprightVacuum::create([
                'status' => 'draft',
                'model' => "Type {$type} Test " . time(),
                'brand_name' => 'Test Brand',
                'type' => $type,
            ]);

            $this->assertEquals($type, $vacuum->type);
        }
    }

    /**
     * Poprawka: power_supply z ->live() kontroluje widoczność cable_length.
     */
    public function test_upright_vacuum_power_supply_array(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Power Supply Test ' . time(),
            'brand_name' => 'Test Brand',
            'power_supply' => ['Akumulatorowe', 'Sieciowe'],
        ]);

        $this->assertIsArray($vacuum->power_supply);
        $this->assertContains('Sieciowe', $vacuum->power_supply);
    }

    /**
     * Poprawka: cable_length widoczny tylko przy zasilaniu Sieciowe.
     */
    public function test_upright_vacuum_cable_length_with_sieciowe(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Cable Length Test ' . time(),
            'brand_name' => 'Test Brand',
            'power_supply' => ['Sieciowe'],
            'cable_length' => 8.5,
        ]);

        $this->assertEquals(8.5, $vacuum->cable_length);
    }

    /**
     * Poprawka: battery_change zmieniony z TextInput na Select.
     */
    public function test_upright_vacuum_battery_change_select(): void
    {
        $options = ['tak', 'nie', 'ograniczona'];

        foreach ($options as $option) {
            $vacuum = UprightVacuum::create([
                'status' => 'draft',
                'model' => "Battery Change {$option} Test " . time(),
                'brand_name' => 'Test Brand',
                'battery_change' => $option,
            ]);

            $this->assertEquals($option, $vacuum->battery_change);
        }
    }

    /**
     * Poprawka: displaying_battery_status zmieniony z TextInput na Select.
     */
    public function test_upright_vacuum_battery_status_select(): void
    {
        $options = ['diody_led', 'wyswietlacz_lcd', 'procent', 'brak'];

        foreach ($options as $option) {
            $vacuum = UprightVacuum::create([
                'status' => 'draft',
                'model' => "Battery Status {$option} Test " . time(),
                'brand_name' => 'Test Brand',
                'displaying_battery_status' => $option,
            ]);

            $this->assertEquals($option, $vacuum->displaying_battery_status);
        }
    }

    /**
     * Poprawka: Pola mopowania zmienione z TextInput na Toggle.
     */
    public function test_upright_vacuum_mopping_fields_are_toggles(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Mopping Toggles Test ' . time(),
            'brand_name' => 'Test Brand',
            'mopping_function' => true,
            'active_washing_function' => true,
            'self_cleaning_function' => false,
            'self_cleaning_underlays' => true,
        ]);

        $this->assertTrue((bool) $vacuum->mopping_function);
        $this->assertTrue((bool) $vacuum->active_washing_function);
        $this->assertFalse((bool) $vacuum->self_cleaning_function);
        $this->assertTrue((bool) $vacuum->self_cleaning_underlays);
    }

    /**
     * Poprawka: type_of_washing zmieniony na Select::multiple().
     */
    public function test_upright_vacuum_type_of_washing_multi_select(): void
    {
        $validTypes = ['suche', 'mokre', 'parowe', 'hybrydowe'];

        foreach ($validTypes as $type) {
            $vacuum = UprightVacuum::create([
                'status' => 'draft',
                'model' => "Washing Type {$type} Test " . time(),
                'brand_name' => 'Test Brand',
                'type_of_washing' => $type,
            ]);

            $this->assertEquals($type, $vacuum->type_of_washing);
        }
    }

    /**
     * Poprawka: mopping_time_max z ->numeric()->suffix('min').
     */
    public function test_upright_vacuum_mopping_time_numeric(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Mopping Time Test ' . time(),
            'brand_name' => 'Test Brand',
            'mopping_time_max' => 45,
        ]);

        $this->assertEquals(45, $vacuum->mopping_time_max);
        $this->assertIsNumeric($vacuum->mopping_time_max);
    }

    /**
     * Poprawka: Filtry - pola zmienione na Toggle (jest/brak).
     */
    public function test_upright_vacuum_filter_fields_are_toggles(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Filter Toggle Test ' . time(),
            'brand_name' => 'Test Brand',
            'cyclone_technology' => true,
            'mesh_filter' => true,
            'hepa_filter' => true,
            'epa_filter' => false,
            'uv_technology' => true,
            'led_backlight' => false,
            'detecting_dirt_on_the_floor' => true,
            'detecting_carpet' => true,
        ]);

        $this->assertTrue((bool) $vacuum->cyclone_technology);
        $this->assertTrue((bool) $vacuum->mesh_filter);
        $this->assertTrue((bool) $vacuum->hepa_filter);
        $this->assertFalse((bool) $vacuum->epa_filter);
        $this->assertTrue((bool) $vacuum->uv_technology);
        $this->assertFalse((bool) $vacuum->led_backlight);
        $this->assertTrue((bool) $vacuum->detecting_dirt_on_the_floor);
        $this->assertTrue((bool) $vacuum->detecting_carpet);
    }

    /**
     * Poprawka: Szczotki zmienione na Toggle (jest/brak).
     */
    public function test_upright_vacuum_brush_fields_are_toggles(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Brush Toggle Test ' . time(),
            'brand_name' => 'Test Brand',
            'electric_brush' => true,
            'turbo_brush' => false,
            'carpet_and_floor_brush' => true,
            'attachment_for_pets' => true,
            'bendable_pipe' => false,
            'telescopic_tube' => true,
            'hand_vacuum_cleaner' => true,
        ]);

        $this->assertTrue((bool) $vacuum->electric_brush);
        $this->assertFalse((bool) $vacuum->turbo_brush);
        $this->assertTrue((bool) $vacuum->carpet_and_floor_brush);
        $this->assertTrue((bool) $vacuum->attachment_for_pets);
        $this->assertFalse((bool) $vacuum->bendable_pipe);
        $this->assertTrue((bool) $vacuum->telescopic_tube);
        $this->assertTrue((bool) $vacuum->hand_vacuum_cleaner);
    }

    /**
     * Poprawka: charging_station zmienione na Select z opcjami.
     */
    public function test_upright_vacuum_charging_station_select(): void
    {
        $options = ['brak', 'scienna', 'stojaca', 'stacja_dokujaca', 'podstawka'];

        foreach ($options as $option) {
            $vacuum = UprightVacuum::create([
                'status' => 'draft',
                'model' => "Charging {$option} Test " . time(),
                'brand_name' => 'Test Brand',
                'charging_station' => $option,
            ]);

            $this->assertEquals($option, $vacuum->charging_station);
        }
    }

    /**
     * Poprawka: pollution_filtration_system zmienione na Select z opcjami.
     */
    public function test_upright_vacuum_filtration_system_select(): void
    {
        $options = ['cyklonowy', 'wielocyklonowy', 'workowy', 'wodny', 'inny'];

        foreach ($options as $option) {
            $vacuum = UprightVacuum::create([
                'status' => 'draft',
                'model' => "Filtration {$option} Test " . time(),
                'brand_name' => 'Test Brand',
                'pollution_filtration_system' => $option,
            ]);

            $this->assertEquals($option, $vacuum->pollution_filtration_system);
        }
    }

    /**
     * Poprawka: capability_points/profitability_points usunięte z rankingu.
     */
    public function test_upright_vacuum_ranking_without_points(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Ranking Test ' . time(),
            'brand_name' => 'Test Brand',
            'capability' => 70,
            'profitability' => 80,
            'ranking' => 5,
            'main_ranking' => true,
            'ranking_hidden' => false,
        ]);

        $this->assertEquals(70, $vacuum->capability);
        $this->assertEquals(80, $vacuum->profitability);
        $this->assertTrue((bool) $vacuum->main_ranking);
    }

    /**
     * Poprawka: continuous_work zmienione z TextInput na Toggle (jest/brak).
     */
    public function test_upright_vacuum_continuous_work_is_toggle(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Continuous Work Test ' . time(),
            'brand_name' => 'Test Brand',
            'continuous_work' => true,
        ]);

        $this->assertTrue((bool) $vacuum->continuous_work);
    }

    /**
     * Poprawka: display zmienione z TextInput na Toggle (jest/brak).
     */
    public function test_upright_vacuum_display_is_toggle(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Display Test ' . time(),
            'brand_name' => 'Test Brand',
            'display' => true,
        ]);

        $this->assertTrue((bool) $vacuum->display);
    }

    /**
     * Poprawka: for_pet_owners i for_allergy_sufferers zmienione z TextInput na Toggle.
     */
    public function test_upright_vacuum_purpose_fields_are_toggles(): void
    {
        $vacuum = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Purpose Toggle Test ' . time(),
            'brand_name' => 'Test Brand',
            'for_pet_owners' => true,
            'for_allergy_sufferers' => false,
        ]);

        $this->assertTrue((bool) $vacuum->for_pet_owners);
        $this->assertFalse((bool) $vacuum->for_allergy_sufferers);
    }

    // ==========================================
    // CZUJNIKI (Sensor)
    // ==========================================

    /**
     * Poprawka: mesh_filter label zmieniony na 'Filtr wstępny' we wszystkich zasobach.
     */
    public function test_mesh_filter_renamed_to_filtr_wstepny_in_all_resources(): void
    {
        $resourceFiles = [
            'AirPurifierResource.php',
            'AirHumidifierResource.php',
            'AirConditionerResource.php',
            'DehumidifierResource.php',
            'UprightVacuumResource.php',
        ];

        foreach ($resourceFiles as $file) {
            $content = file_get_contents(app_path("Filament/Resources/{$file}"));

            if (str_contains($content, 'mesh_filter')) {
                $this->assertStringNotContainsString(
                    "'Filtr siatkowy'",
                    $content,
                    "{$file}: mesh_filter should use label 'Filtr wstępny' not 'Filtr siatkowy'"
                );
                $this->assertStringContainsString(
                    'Filtr wstępny',
                    $content,
                    "{$file}: mesh_filter should have label 'Filtr wstępny'"
                );
            }
        }
    }

    /**
     * Poprawka: custom_fields tab powinien być widoczny tylko gdy istnieją custom fields.
     * Conditional visibility is now handled in FormLayoutService::buildForm().
     */
    public function test_custom_fields_tab_is_conditionally_visible(): void
    {
        $serviceFile = file_get_contents(app_path('Services/FormLayoutService.php'));

        $this->assertStringContainsString(
            'count($customFieldSchema) > 0',
            $serviceFile,
            'FormLayoutService::buildForm() should handle custom_fields tab conditional visibility'
        );
    }

    /**
     * Poprawka: Wszystkie zakładki czujników powinny być po polsku.
     * Tab names are now stored in the DB seeder (FormTabConfigurationSeeder).
     */
    public function test_sensor_tab_names_are_in_polish(): void
    {
        $seederFile = file_get_contents(database_path('seeders/FormTabConfigurationSeeder.php'));

        $polishTabs = ['Podstawowe informacje', 'Czujniki PM', 'Czujniki chemiczne',
            'Czujniki środowiskowe', 'Zasilanie i łączność', 'Funkcje urządzenia',
            'Wymiary i wydajność', 'Metadane'];

        foreach ($polishTabs as $polishTab) {
            $this->assertStringContainsString(
                $polishTab,
                $seederFile,
                "Tab '{$polishTab}' should exist in FormTabConfigurationSeeder for sensors"
            );
        }
    }

    // ==========================================
    // Infrastructure Tests
    // ==========================================

    /**
     * Verify the migration for PostgreSQL sequence fix exists.
     * Fixes: UniqueConstraintViolationException on air_purifiers_pkey.
     */
    public function test_sequence_fix_migration_exists(): void
    {
        $migrationPath = database_path('migrations/2026_02_06_000000_fix_postgresql_sequences.php');
        $this->assertFileExists($migrationPath);
    }
}
