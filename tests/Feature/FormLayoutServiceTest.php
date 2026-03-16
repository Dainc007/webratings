<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\FormLayoutItem;
use App\Models\LabelOverride;
use App\Services\FormLayoutService;
use App\Services\LabelService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FormLayoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        FormLayoutService::clearCache();
        LabelService::clearCache();
    }

    public function test_returns_default_tabs_when_no_db_layout_exists(): void
    {
        $defaultTabs = [
            Tab::make('Tab A')->schema([TextInput::make('field_a')]),
            Tab::make('Tab B')->schema([TextInput::make('field_b')]),
        ];

        $result = FormLayoutService::applyLayout('air_purifiers', $defaultTabs);

        $this->assertCount(2, $result);
        $this->assertSame($defaultTabs, $result);
    }

    public function test_form_layout_item_model_has_correct_fillable(): void
    {
        $item = new FormLayoutItem();

        $this->assertEquals(
            ['table_name', 'element_type', 'element_key', 'parent_key', 'sort_order'],
            $item->getFillable()
        );
    }

    public function test_form_layout_item_has_integer_sort_order_cast(): void
    {
        $item = new FormLayoutItem();

        $this->assertArrayHasKey('sort_order', $item->getCasts());
        $this->assertEquals('integer', $item->getCasts()['sort_order']);
    }

    public function test_seed_default_layout_creates_items(): void
    {
        $structure = [
            'Tab A' => [
                'sections' => [
                    'Section 1' => ['field_a', 'field_b'],
                    'Section 2' => ['field_c'],
                ],
            ],
            'Tab B' => [
                'sections' => [
                    'Section 3' => ['field_d'],
                ],
            ],
        ];

        $count = FormLayoutService::seedDefaultLayout('test_table', $structure);

        $this->assertEquals(9, $count);
        $this->assertDatabaseHas('form_layout_items', [
            'table_name' => 'test_table',
            'element_type' => 'tab',
            'element_key' => 'Tab A',
            'sort_order' => 0,
        ]);
        $this->assertDatabaseHas('form_layout_items', [
            'table_name' => 'test_table',
            'element_type' => 'section',
            'element_key' => 'Section 1',
            'parent_key' => 'Tab A',
            'sort_order' => 0,
        ]);
        $this->assertDatabaseHas('form_layout_items', [
            'table_name' => 'test_table',
            'element_type' => 'field',
            'element_key' => 'field_b',
            'parent_key' => 'Section 1',
            'sort_order' => 1,
        ]);
    }

    public function test_get_structure_returns_empty_when_no_layout(): void
    {
        $result = FormLayoutService::getStructure('nonexistent_table');

        $this->assertEmpty($result);
    }

    public function test_get_structure_returns_correct_hierarchy(): void
    {
        FormLayoutItem::create(['table_name' => 'test', 'element_type' => 'tab', 'element_key' => 'Tab A', 'parent_key' => null, 'sort_order' => 0]);
        FormLayoutItem::create(['table_name' => 'test', 'element_type' => 'section', 'element_key' => 'Sec 1', 'parent_key' => 'Tab A', 'sort_order' => 0]);
        FormLayoutItem::create(['table_name' => 'test', 'element_type' => 'field', 'element_key' => 'name', 'parent_key' => 'Sec 1', 'sort_order' => 0]);
        FormLayoutItem::create(['table_name' => 'test', 'element_type' => 'field', 'element_key' => 'email', 'parent_key' => 'Sec 1', 'sort_order' => 1]);

        $structure = FormLayoutService::getStructure('test');

        $this->assertArrayHasKey('Tab A', $structure);
        $this->assertEquals(0, $structure['Tab A']['sort_order']);
        $this->assertArrayHasKey('Sec 1', $structure['Tab A']['sections']);
        $this->assertCount(2, $structure['Tab A']['sections']['Sec 1']['fields']);
        $this->assertEquals(0, $structure['Tab A']['sections']['Sec 1']['fields']['name']);
        $this->assertEquals(1, $structure['Tab A']['sections']['Sec 1']['fields']['email']);
    }

    public function test_cache_is_cleared_after_seed(): void
    {
        FormLayoutService::getStructure('test_cache');
        $this->assertEmpty(FormLayoutService::getStructure('test_cache'));

        FormLayoutService::seedDefaultLayout('test_cache', [
            'Tab' => ['sections' => ['Sec' => ['field']]],
        ]);

        $this->assertNotEmpty(FormLayoutService::getStructure('test_cache'));
    }

    public function test_seed_uses_update_or_create_for_idempotency(): void
    {
        $structure = [
            'Tab' => ['sections' => ['Sec' => ['field']]],
        ];

        FormLayoutService::seedDefaultLayout('idempotent_test', $structure);
        FormLayoutService::seedDefaultLayout('idempotent_test', $structure);

        $count = FormLayoutItem::where('table_name', 'idempotent_test')->count();
        $this->assertEquals(3, $count);
    }

    public function test_sort_order_in_label_override_is_nullable(): void
    {
        $override = LabelOverride::create([
            'table_name' => 'air_purifiers',
            'element_type' => 'field',
            'element_key' => 'test_field',
            'display_label' => 'Test',
        ]);

        $this->assertNull($override->sort_order);
    }

    public function test_sort_order_returns_value_from_label_service(): void
    {
        LabelOverride::create([
            'table_name' => 'air_purifiers',
            'element_type' => 'field',
            'element_key' => 'brand_name',
            'display_label' => null,
            'sort_order' => 5,
        ]);

        $sortOrder = LabelService::sortOrder('air_purifiers', 'field', 'brand_name');

        $this->assertEquals(5, $sortOrder);
    }

    public function test_sort_order_returns_null_when_not_set(): void
    {
        $sortOrder = LabelService::sortOrder('air_purifiers', 'field', 'nonexistent');

        $this->assertNull($sortOrder);
    }

    public function test_form_layout_items_migration_creates_table(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable('form_layout_items'),
            'form_layout_items table should exist'
        );
    }

    public function test_form_layout_items_table_has_required_columns(): void
    {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('form_layout_items');

        $this->assertContains('id', $columns);
        $this->assertContains('table_name', $columns);
        $this->assertContains('element_type', $columns);
        $this->assertContains('element_key', $columns);
        $this->assertContains('parent_key', $columns);
        $this->assertContains('sort_order', $columns);
    }

    public function test_label_overrides_table_has_sort_order_column(): void
    {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('label_overrides');

        $this->assertContains('sort_order', $columns);
    }

    public function test_all_resources_use_apply_layout(): void
    {
        $resources = [
            'AirPurifierResource',
            'AirHumidifierResource',
            'AirConditionerResource',
            'DehumidifierResource',
            'UprightVacuumResource',
            'SensorResource',
        ];

        foreach ($resources as $resource) {
            $path = app_path("Filament/Resources/{$resource}.php");
            $content = file_get_contents($path);

            $this->assertStringContainsString(
                'FormLayoutService::applyLayout',
                $content,
                "{$resource} should use FormLayoutService::applyLayout()"
            );
            $this->assertStringContainsString(
                'use App\Services\FormLayoutService;',
                $content,
                "{$resource} should import FormLayoutService"
            );
        }
    }

    public function test_form_layout_editor_page_exists(): void
    {
        $this->assertFileExists(
            app_path('Filament/Pages/FormLayoutEditor.php'),
            'FormLayoutEditor page should exist'
        );
    }

    public function test_form_layout_editor_view_exists(): void
    {
        $this->assertFileExists(
            resource_path('views/filament/pages/form-layout-editor.blade.php'),
            'FormLayoutEditor view should exist'
        );
    }
}
