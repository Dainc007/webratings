<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\AirConditionerResource\Pages\CreateAirConditioner;
use App\Filament\Resources\AirConditionerResource\Pages\EditAirConditioner;
use App\Filament\Resources\AirConditionerResource\Pages\ListAirConditioners;
use App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\EditAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\ListAirHumidifiers;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\ListAirPurifiers;
use App\Filament\Resources\CustomFieldResource\Pages\CreateCustomField;
use App\Filament\Resources\CustomFieldResource\Pages\EditCustomField;
use App\Filament\Resources\CustomFieldResource\Pages\ListCustomFields;
use App\Filament\Resources\DehumidifierResource\Pages\CreateDehumidifier;
use App\Filament\Resources\DehumidifierResource\Pages\EditDehumidifier;
use App\Filament\Resources\DehumidifierResource\Pages\ListDehumidifiers;
use App\Filament\Resources\SensorResource\Pages\CreateSensor;
use App\Filament\Resources\SensorResource\Pages\EditSensor;
use App\Filament\Resources\SensorResource\Pages\ListSensors;
use App\Filament\Resources\ShortcodeResource\Pages\CreateShortcode;
use App\Filament\Resources\ShortcodeResource\Pages\EditShortcode;
use App\Filament\Resources\ShortcodeResource\Pages\ListShortcodes;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\EditUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\ListUprightVacuums;
use App\Models\AirConditioner;
use App\Models\AirHumidifier;
use App\Models\AirPurifier;
use App\Models\CustomField;
use App\Models\Dehumidifier;
use App\Models\Sensor;
use App\Models\Shortcode;
use App\Models\UprightVacuum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Smoke tests for all Filament resource pages.
 *
 * These tests ensure no 500 errors occur on any resource page (list, create, edit).
 * They catch issues like:
 * - "Array to string conversion" from cast/form mismatches
 * - Missing columns or broken migrations
 * - Form hydration errors on edit pages
 * - View rendering errors
 *
 * Run with: php artisan test --filter=ResourcePageSmokeTest
 */
#[Group('filament-smoke')]
class ResourcePageSmokeTest extends TestCase
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
    // List Pages — no data needed
    // ==========================================

    #[DataProvider('listPageProvider')]
    public function test_list_page_renders_successfully(string $pageClass): void
    {
        Livewire::test($pageClass)->assertSuccessful();
    }

    public static function listPageProvider(): array
    {
        return [
            'AirPurifiers' => [ListAirPurifiers::class],
            'AirHumidifiers' => [ListAirHumidifiers::class],
            'AirConditioners' => [ListAirConditioners::class],
            'Dehumidifiers' => [ListDehumidifiers::class],
            'Sensors' => [ListSensors::class],
            'UprightVacuums' => [ListUprightVacuums::class],
            'CustomFields' => [ListCustomFields::class],
            'Shortcodes' => [ListShortcodes::class],
        ];
    }

    // ==========================================
    // Create Pages — no data needed
    // ==========================================

    #[DataProvider('createPageProvider')]
    public function test_create_page_renders_successfully(string $pageClass): void
    {
        Livewire::test($pageClass)->assertSuccessful();
    }

    public static function createPageProvider(): array
    {
        return [
            'AirPurifier' => [CreateAirPurifier::class],
            'AirHumidifier' => [CreateAirHumidifier::class],
            'AirConditioner' => [CreateAirConditioner::class],
            'Dehumidifier' => [CreateDehumidifier::class],
            'Sensor' => [CreateSensor::class],
            'UprightVacuum' => [CreateUprightVacuum::class],
            'CustomField' => [CreateCustomField::class],
            'Shortcode' => [CreateShortcode::class],
        ];
    }

    // ==========================================
    // Edit Pages — tests form hydration with real data
    // This is the critical section that catches cast/form mismatches.
    // ==========================================

    public function test_edit_air_purifier_with_array_fields(): void
    {
        $record = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Smoke Test ' . time(),
            'brand_name' => 'Test Brand',
            'partner_link_rel_2' => ['nofollow', 'sponsored'],
            'ceneo_link_rel_2' => ['nofollow'],
            'colors' => ['biały', 'czarny'],
            'functions_and_equipment' => ['jonizacja', 'nawilżanie'],
            'certificates' => ['CE'],
        ]);

        Livewire::test(EditAirPurifier::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_air_humidifier_with_array_fields(): void
    {
        $record = AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Smoke Test ' . time(),
            'brand_name' => 'Test Brand',
            'colors' => ['biały'],
            'mobile_features' => ['wifi', 'bluetooth'],
        ]);

        Livewire::test(EditAirHumidifier::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_air_conditioner_with_array_fields(): void
    {
        $record = AirConditioner::create([
            'status' => 'draft',
            'model' => 'Smoke Test ' . time(),
            'brand_name' => 'Test Brand',
            'partner_link_rel_2' => ['nofollow'],
            'ceneo_link_rel_2' => ['sponsored'],
            'colors' => ['biały'],
            'mobile_features' => ['wifi'],
            'functions_and_equipment_condi' => ['grzanie'],
        ]);

        Livewire::test(EditAirConditioner::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_dehumidifier_with_array_fields(): void
    {
        $record = Dehumidifier::create([
            'status' => 'draft',
            'model' => 'Smoke Test ' . time(),
            'brand_name' => 'Test Brand',
            'partner_link_rel_2' => ['nofollow'],
            'ceneo_link_rel_2' => ['sponsored'],
            'mobile_features' => ['wifi'],
            'modes_of_operation' => ['auto', 'manual'],
            'colors' => ['biały'],
        ]);

        Livewire::test(EditDehumidifier::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_sensor_with_array_fields(): void
    {
        $record = Sensor::create([
            'status' => 'draft',
            'model' => 'Smoke Test ' . time(),
            'brand_name' => 'Test Brand',
            'partner_link_rel_2' => ['nofollow'],
            'ceneo_link_rel_2' => ['sponsored'],
            'mobile_features' => ['wifi', 'bluetooth'],
        ]);

        Livewire::test(EditSensor::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_upright_vacuum_with_array_fields(): void
    {
        $record = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Smoke Test ' . time(),
            'brand_name' => 'Test Brand',
            'partner_link_rel_2' => ['nofollow', 'sponsored'],
            'ceneo_link_rel_2' => ['nofollow'],
            'vacuum_cleaner_type' => ['bezworkowy', 'pionowy'],
            'power_supply' => ['Akumulatorowe'],
            'display_type' => ['LCD', 'LED'],
            'charging_station' => ['scienna', 'stojaca'],
            'type_of_washing' => ['suche', 'mokre'],
            'additional_equipment' => ['szczotka', 'filtr'],
            'colors' => ['czarny', 'srebrny'],
        ]);

        Livewire::test(EditUprightVacuum::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_custom_field(): void
    {
        $record = CustomField::create([
            'table_name' => 'air_purifiers',
            'column_type' => 'string',
            'column_name' => 'smoke_test_field',
            'display_name' => 'Smoke Test Field',
        ]);

        Livewire::test(EditCustomField::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_shortcode_with_array_fields(): void
    {
        $record = Shortcode::create([
            'name' => 'Smoke Test Shortcode ' . time(),
            'product_types' => ['air_purifiers', 'air_humidifiers'],
        ]);

        Livewire::test(EditShortcode::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    // ==========================================
    // Edit Pages — null/empty array edge cases
    // Ensures empty and null arrays don't crash hydration.
    // ==========================================

    public function test_edit_upright_vacuum_with_null_array_fields(): void
    {
        $record = UprightVacuum::create([
            'status' => 'draft',
            'model' => 'Null Arrays Test ' . time(),
            'brand_name' => 'Test Brand',
            // All array fields left as null
        ]);

        Livewire::test(EditUprightVacuum::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_dehumidifier_with_null_array_fields(): void
    {
        $record = Dehumidifier::create([
            'status' => 'draft',
            'model' => 'Null Arrays Test ' . time(),
            'brand_name' => 'Test Brand',
        ]);

        Livewire::test(EditDehumidifier::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    public function test_edit_air_purifier_with_null_array_fields(): void
    {
        $record = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Null Arrays Test ' . time(),
            'brand_name' => 'Test Brand',
        ]);

        Livewire::test(EditAirPurifier::class, ['record' => $record->id])
            ->assertSuccessful();
    }

    // ==========================================
    // Structural Meta-Tests
    // Validate that model casts and form Select fields are aligned.
    // ==========================================

    /**
     * Verify that every model field with 'array' cast that uses Select::make()
     * in its resource form also has ->multiple() declared.
     *
     * This meta-test prevents the "Array to string conversion" class of bugs
     * at the code level, without needing to render any pages.
     */
    public function test_all_array_cast_select_fields_have_multiple(): void
    {
        $resources = [
            ['model' => new AirPurifier, 'resource' => 'AirPurifierResource.php'],
            ['model' => new AirHumidifier, 'resource' => 'AirHumidifierResource.php'],
            ['model' => new AirConditioner, 'resource' => 'AirConditionerResource.php'],
            ['model' => new Dehumidifier, 'resource' => 'DehumidifierResource.php'],
            ['model' => new Sensor, 'resource' => 'SensorResource.php'],
            ['model' => new UprightVacuum, 'resource' => 'UprightVacuumResource.php'],
        ];

        // Fields that use TagsInput/FileUpload instead of Select — skip these
        $nonSelectArrayFields = [
            'colors', 'additional_equipment', 'functions_and_equipment',
            'certificates', 'gallery', 'mobile_features',
            'functions_and_equipment_condi', 'functions_and_equipment_dehumi',
            'modes_of_operation', 'higrostat', 'uv_light_generator',
            'control_other',
        ];

        foreach ($resources as $entry) {
            $model = $entry['model'];
            $resourceFile = $entry['resource'];
            $resourcePath = app_path("Filament/Resources/{$resourceFile}");
            $content = file_get_contents($resourcePath);
            $casts = $model->getCasts();

            foreach ($casts as $field => $castType) {
                if ($castType !== 'array') {
                    continue;
                }

                if (in_array($field, $nonSelectArrayFields, true)) {
                    continue;
                }

                // Check if this field uses Select::make() in the resource
                if (! preg_match("/Select::make\('{$field}'\)/", $content)) {
                    continue; // Not a Select field, skip
                }

                // It IS a Select field with array cast — it MUST have ->multiple()
                $pattern = "/Select::make\('{$field}'\).*?->multiple\(\)/s";
                $this->assertMatchesRegularExpression(
                    $pattern,
                    $content,
                    "[{$resourceFile}] Select::make('{$field}') has array cast but is missing ->multiple(). " .
                    'This WILL cause "Array to string conversion" on the edit page.'
                );
            }
        }
    }

    /**
     * Verify that every Select::make() with ->multiple() in a resource
     * has a corresponding 'array' cast in the model.
     *
     * This catches the reverse mismatch: multiple-select without array cast
     * would crash when trying to save an array to a string column.
     */
    public function test_all_multiple_select_fields_have_array_cast(): void
    {
        $resources = [
            ['model' => new AirPurifier, 'resource' => 'AirPurifierResource.php'],
            ['model' => new AirHumidifier, 'resource' => 'AirHumidifierResource.php'],
            ['model' => new AirConditioner, 'resource' => 'AirConditionerResource.php'],
            ['model' => new Dehumidifier, 'resource' => 'DehumidifierResource.php'],
            ['model' => new Sensor, 'resource' => 'SensorResource.php'],
            ['model' => new UprightVacuum, 'resource' => 'UprightVacuumResource.php'],
        ];

        // Relationship fields use ->multiple() but don't need array cast
        $relationshipFields = ['types', 'productFunctions'];

        foreach ($resources as $entry) {
            $model = $entry['model'];
            $resourceFile = $entry['resource'];
            $resourcePath = app_path("Filament/Resources/{$resourceFile}");
            $content = file_get_contents($resourcePath);
            $casts = $model->getCasts();

            // Find all Select::make('field')->...->multiple() patterns
            preg_match_all(
                "/Select::make\('(\w+)'\)(?:(?!Select::make).)*?->multiple\(\)/s",
                $content,
                $matches
            );

            foreach ($matches[1] as $field) {
                if (in_array($field, $relationshipFields, true)) {
                    continue; // Relationships don't need array cast
                }

                $this->assertArrayHasKey(
                    $field,
                    $casts,
                    "[{$resourceFile}] Select::make('{$field}') has ->multiple() but the model " .
                    "does not cast it as 'array'. This WILL cause 'Array to string conversion' on save."
                );

                $this->assertEquals(
                    'array',
                    $casts[$field],
                    "[{$resourceFile}] Select::make('{$field}') has ->multiple() but the model " .
                    "casts it as '{$casts[$field]}' instead of 'array'."
                );
            }
        }
    }
}
