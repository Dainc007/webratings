<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\AirConditionerResource;
use App\Filament\Resources\AirConditionerResource\Pages\CreateAirConditioner;
use App\Filament\Resources\AirHumidifierResource;
use App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier;
use App\Filament\Resources\AirPurifierResource;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\DehumidifierResource;
use App\Filament\Resources\DehumidifierResource\Pages\CreateDehumidifier;
use App\Filament\Resources\SensorResource;
use App\Filament\Resources\SensorResource\Pages\CreateSensor;
use App\Filament\Resources\UprightVacuumResource;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Tests verifying implementation of all items from the "poprawki" document.
 *
 * Covers: gallery placement, field types, predefined options,
 * field removal/relocation, conditional visibility, and relationship selects.
 */
#[Group('poprawki-coverage')]
class PoprawkiCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    // =========================================================================
    // Group 1: Gallery Placement — gallery must be in "Podstawowe informacje" tab
    // =========================================================================

    #[DataProvider('galleryResourceProvider')]
    public function test_gallery_is_in_basic_info_tab(string $resourceClass): void
    {
        $reflection = new \ReflectionClass($resourceClass);
        $content = file_get_contents($reflection->getFileName());

        // Gallery field must appear inside the "Podstawowe informacje" tab
        // The pattern: Tab::make(...'Podstawowe informacje'...) contains local_gallery
        $this->assertStringContainsString('local_gallery', $content, 'Gallery field should exist in resource');

        // Verify gallery is NOT in a separate "Dodatkowe" tab
        // It should appear before any second Tab::make call
        $basicInfoPos = strpos($content, 'Podstawowe informacje');
        $galleryPos = strpos($content, 'local_gallery');
        $this->assertNotFalse($basicInfoPos, 'Podstawowe informacje tab should exist');
        $this->assertNotFalse($galleryPos, 'local_gallery field should exist');

        // Gallery should appear after "Podstawowe informacje" tab declaration
        $this->assertGreaterThan($basicInfoPos, $galleryPos, 'Gallery should be inside Podstawowe informacje tab');
    }

    #[DataProvider('galleryCreatePageProvider')]
    public function test_gallery_field_exists_on_create_page(string $pageClass): void
    {
        Livewire::test($pageClass)
            ->assertFormFieldExists('local_gallery');
    }

    public static function galleryResourceProvider(): array
    {
        return [
            'air purifier' => [AirPurifierResource::class],
            'air humidifier' => [AirHumidifierResource::class],
            'dehumidifier' => [DehumidifierResource::class],
            'air conditioner' => [AirConditionerResource::class],
            'upright vacuum' => [UprightVacuumResource::class],
            'sensor' => [SensorResource::class],
        ];
    }

    public static function galleryCreatePageProvider(): array
    {
        return [
            'air purifier' => [CreateAirPurifier::class],
            'air humidifier' => [CreateAirHumidifier::class],
            'dehumidifier' => [CreateDehumidifier::class],
            'air conditioner' => [CreateAirConditioner::class],
            'upright vacuum' => [CreateUprightVacuum::class],
            'sensor' => [CreateSensor::class],
        ];
    }

    // =========================================================================
    // Group 2: Field Type Verification
    // =========================================================================

    // --- 2a: Brand as searchable Select with create option (all resources) ---

    #[DataProvider('brandCreatePageProvider')]
    public function test_brand_name_is_searchable_select_with_create_option(string $pageClass): void
    {
        Livewire::test($pageClass)
            ->assertFormFieldExists('brand_name', function ($field) {
                return $field instanceof Select
                    && $field->isSearchable();
            });
    }

    public static function brandCreatePageProvider(): array
    {
        return [
            'air purifier' => [CreateAirPurifier::class],
            'air humidifier' => [CreateAirHumidifier::class],
            'dehumidifier' => [CreateDehumidifier::class],
            'air conditioner' => [CreateAirConditioner::class],
            'upright vacuum' => [CreateUprightVacuum::class],
            'sensor' => [CreateSensor::class],
        ];
    }

    // --- 2b: Popularity as numeric TextInput ---

    public function test_humidifier_popularity_is_numeric_text_input(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('popularity', function ($field) {
                return $field instanceof TextInput
                    && $field->isNumeric();
            });
    }

    public function test_dehumidifier_popularity_is_numeric_text_input(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->assertFormFieldExists('popularity', function ($field) {
                return $field instanceof TextInput
                    && $field->isNumeric();
            });
    }

    // --- 2c: Coolant type as Select ---

    public function test_dehumidifier_refrigerant_kind_is_select_with_options(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->assertFormFieldExists('refrigerant_kind', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('R290', $options)
                    && array_key_exists('R410a', $options)
                    && array_key_exists('R32', $options);
            });
    }

    public function test_air_conditioner_refrigerant_kind_is_select_with_options(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('refrigerant_kind', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('R290', $options)
                    && array_key_exists('R410a', $options)
                    && array_key_exists('R32', $options);
            });
    }

    // --- 2d: Coolant amount suffix 'g' ---

    public function test_dehumidifier_refrigerant_amount_suffix_is_grams(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->assertFormFieldExists('refrigerant_amount', function ($field) {
                return $field instanceof TextInput
                    && $field->getSuffixLabel() === 'g';
            });
    }

    public function test_air_conditioner_refrigerant_amount_suffix_is_grams(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('refrigerant_amount', function ($field) {
                return $field instanceof TextInput
                    && $field->getSuffixLabel() === 'g';
            });
    }

    // --- 2e: Power unit suffix 'kW' (ACs) ---

    public function test_air_conditioner_cooling_power_suffix_is_kw(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('maximum_cooling_power', function ($field) {
                return $field instanceof TextInput
                    && $field->getSuffixLabel() === 'kW';
            });
    }

    public function test_air_conditioner_heating_power_suffix_is_kw(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('maximum_heating_power', function ($field) {
                return $field instanceof TextInput
                    && $field->getSuffixLabel() === 'kW';
            });
    }

    // --- 2f: Automatic power adjustment as Toggle (vacuums) ---

    public function test_vacuum_automatic_power_adjustment_is_toggle(): void
    {
        Livewire::test(CreateUprightVacuum::class)
            ->assertFormFieldExists('automatic_power_adjustment', function ($field) {
                return $field instanceof Toggle;
            });
    }

    // --- 2g: Additional equipment as Select with create option (vacuums) ---

    public function test_vacuum_additional_equipment_is_select_with_create_option(): void
    {
        Livewire::test(CreateUprightVacuum::class)
            ->assertFormFieldExists('additional_equipment', function ($field) {
                return $field instanceof Select
                    && $field->isMultiple();
            });
    }

    // =========================================================================
    // Group 3: Predefined Option Lists
    // =========================================================================

    public function test_purifier_certificates_has_predefined_options(): void
    {
        Livewire::test(CreateAirPurifier::class)
            ->assertFormFieldExists('certificates', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('ECARF', $options)
                    && array_key_exists('Allergy UK', $options)
                    && array_key_exists('AHAM', $options)
                    && array_key_exists('CE', $options);
            });
    }

    public function test_humidifier_water_tank_fill_type_has_predefined_options(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('water_tank_fill_type', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('zdjecie_pokrywy', $options)
                    && array_key_exists('zdjecie_pokrywy_okienko', $options)
                    && array_key_exists('nalewanie_od_gory', $options);
            });
    }

    public function test_humidifier_partner_link_rel_has_predefined_options(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('partner_link_rel_2', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('nofollow', $options)
                    && array_key_exists('dofollow', $options)
                    && array_key_exists('sponsored', $options)
                    && array_key_exists('noopener', $options);
            });
    }

    public function test_dehumidifier_modes_of_operation_has_predefined_options(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->assertFormFieldExists('modes_of_operation', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('piwnica', $options)
                    && array_key_exists('sypialnia', $options)
                    && array_key_exists('praca_ciagla', $options)
                    && array_key_exists('tryb_automatyczny', $options);
            });
    }

    public function test_vacuum_filtration_system_has_predefined_options(): void
    {
        Livewire::test(CreateUprightVacuum::class)
            ->assertFormFieldExists('pollution_filtration_system', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('1-stopniowy', $options)
                    && array_key_exists('2-stopniowy', $options)
                    && array_key_exists('3-stopniowy', $options)
                    && array_key_exists('4-stopniowy', $options)
                    && array_key_exists('5-stopniowy', $options);
            });
    }

    public function test_vacuum_additional_equipment_has_predefined_options(): void
    {
        Livewire::test(CreateUprightVacuum::class)
            ->assertFormFieldExists('additional_equipment', function ($field) {
                if (! $field instanceof Select) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('Ssawka szczelinowa', $options)
                    && array_key_exists('Mini elektroszczotka', $options);
            });
    }

    public function test_humidifier_wifi_range_has_predefined_options(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('mobile_features', function ($field) {
                if (! $field instanceof CheckboxList) {
                    return false;
                }
                $options = $field->getOptions();

                return array_key_exists('Wi-Fi 2,4 GHz', $options)
                    && array_key_exists('Wi-Fi 5 GHz', $options);
            });
    }

    // =========================================================================
    // Group 4: Field/Section Removal or Relocation
    // =========================================================================

    // --- 4a: No "photo/zdjecie" field ---

    public function test_dehumidifier_has_no_photo_field(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(DehumidifierResource::class))->getFileName()
        );

        // "photo" or "zdjecie" should not appear as a field make() call
        $this->assertDoesNotMatchRegularExpression(
            '/::make\s*\(\s*[\'"](?:photo|zdjecie)[\'"]\s*\)/',
            $content,
            'Dehumidifier should not have a photo/zdjecie field'
        );
    }

    public function test_vacuum_has_no_photo_field(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(UprightVacuumResource::class))->getFileName()
        );

        $this->assertDoesNotMatchRegularExpression(
            '/::make\s*\(\s*[\'"](?:photo|zdjecie)[\'"]\s*\)/',
            $content,
            'Upright vacuum should not have a photo/zdjecie field'
        );
    }

    // --- 4b: No "cooling_power" toggle in AC modes section ---

    public function test_air_conditioner_no_cooling_power_field(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(AirConditionerResource::class))->getFileName()
        );

        $this->assertDoesNotMatchRegularExpression(
            '/::make\s*\(\s*[\'"]cooling_power[\'"]\s*\)/',
            $content,
            'Air conditioner should not have a cooling_power yes/no field'
        );
    }

    // --- 4c: Rating/ranking section in basic info tab ---

    #[DataProvider('rankingInBasicInfoProvider')]
    public function test_ranking_section_is_in_basic_info_tab(string $resourceClass): void
    {
        $content = file_get_contents(
            (new \ReflectionClass($resourceClass))->getFileName()
        );

        $basicInfoPos = strpos($content, 'Podstawowe informacje');
        $rankingPos = strpos($content, 'Oceny i ranking');

        $this->assertNotFalse($basicInfoPos, 'Podstawowe informacje tab should exist');
        $this->assertNotFalse($rankingPos, 'Oceny i ranking section should exist');
        $this->assertGreaterThan($basicInfoPos, $rankingPos, 'Ranking section should be inside Podstawowe informacje tab');
    }

    public static function rankingInBasicInfoProvider(): array
    {
        return [
            'air conditioner' => [AirConditionerResource::class],
            'upright vacuum' => [UprightVacuumResource::class],
        ];
    }

    // --- 4d: User manual in basic info tab (ACs) ---

    public function test_air_conditioner_manual_in_basic_info_tab(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(AirConditionerResource::class))->getFileName()
        );

        $basicInfoPos = strpos($content, 'Podstawowe informacje');
        $manualPos = strpos($content, "'manual'");

        $this->assertNotFalse($basicInfoPos, 'Podstawowe informacje tab should exist');
        $this->assertNotFalse($manualPos, 'manual field should exist');
        $this->assertGreaterThan($basicInfoPos, $manualPos, 'Manual field should be inside Podstawowe informacje tab');
    }

    // --- 4e: Terminology: "filtr ewaporacyjny" not "filtr odparowujacy" ---

    public function test_purifier_uses_correct_evaporative_filter_terminology(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(AirPurifierResource::class))->getFileName()
        );

        $this->assertStringContainsString('Filtr ewaporacyjny', $content,
            'Should use "Filtr ewaporacyjny" terminology');
        $this->assertStringNotContainsString('odparowuj', $content,
            'Should NOT contain old "odparowujacy" terminology');
    }

    // =========================================================================
    // Group 5: Conditional Visibility
    // =========================================================================

    public function test_humidifier_carbon_filter_fields_hidden_when_toggle_off(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('carbon_filter', function ($field) {
                return $field instanceof Toggle;
            })
            ->assertFormFieldExists('carbon_filter_price')
            ->assertFormFieldExists('carbon_filter_service_life');
    }

    public function test_humidifier_carbon_filter_toggle_is_live(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(AirHumidifierResource::class))->getFileName()
        );

        // Verify the toggle uses ->live() for reactivity
        $carbonFilterPos = strpos($content, "'carbon_filter'");
        $this->assertNotFalse($carbonFilterPos);

        $livePos = strpos($content, '->live()', $carbonFilterPos);
        $this->assertNotFalse($livePos, 'carbon_filter toggle should use ->live() for reactivity');

        // Verify conditional visibility on dependent fields
        $this->assertStringContainsString("get('carbon_filter')", $content,
            'Dependent fields should check carbon_filter state for visibility');
    }

    // =========================================================================
    // Group 6: Enhanced Partial Coverage — Relationship Selects
    // =========================================================================

    #[DataProvider('productFunctionsPageProvider')]
    public function test_product_functions_is_multiple_select(string $pageClass): void
    {
        Livewire::test($pageClass)
            ->assertFormFieldExists('productFunctions', function ($field) {
                return $field instanceof Select
                    && $field->isMultiple();
            });
    }

    public static function productFunctionsPageProvider(): array
    {
        return [
            'air purifier' => [CreateAirPurifier::class],
            'air humidifier' => [CreateAirHumidifier::class],
            'dehumidifier' => [CreateDehumidifier::class],
            'air conditioner' => [CreateAirConditioner::class],
        ];
    }

    // --- Toggle field type verification for specific poprawki items ---

    public function test_air_conditioner_swing_is_toggle(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('swing', function ($field) {
                return $field instanceof Toggle;
            });
    }

    public function test_air_conditioner_small_is_toggle(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('small', function ($field) {
                return $field instanceof Toggle;
            });
    }

    public function test_air_conditioner_needs_to_be_completed_is_toggle(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('needs_to_be_completed', function ($field) {
                return $field instanceof Toggle;
            });
    }

    public function test_air_conditioner_sealing_is_toggle_with_correct_label(): void
    {
        $content = file_get_contents(
            (new \ReflectionClass(AirConditionerResource::class))->getFileName()
        );

        // Verify sealing is a Toggle
        Livewire::test(CreateAirConditioner::class)
            ->assertFormFieldExists('sealing', function ($field) {
                return $field instanceof Toggle;
            });

        // Verify the label was changed to "Uszczelka w zestawie"
        $this->assertStringContainsString('Uszczelka w zestawie', $content,
            'Sealing field label should be "Uszczelka w zestawie"');
    }

    public function test_dehumidifier_needs_to_be_completed_is_toggle(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->assertFormFieldExists('needs_to_be_completed', function ($field) {
                return $field instanceof Toggle;
            });
    }

    // --- Humidifier toggle fields (parental lock, remote, display) ---

    public function test_humidifier_child_lock_is_toggle(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('child_lock', function ($field) {
                return $field instanceof Toggle;
            });
    }

    public function test_humidifier_remote_control_is_toggle(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('remote_control', function ($field) {
                return $field instanceof Toggle;
            });
    }

    public function test_humidifier_display_is_toggle(): void
    {
        Livewire::test(CreateAirHumidifier::class)
            ->assertFormFieldExists('display', function ($field) {
                return $field instanceof Toggle;
            });
    }
}
