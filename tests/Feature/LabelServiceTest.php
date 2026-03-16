<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LabelOverride;
use App\Services\LabelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LabelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        LabelService::clearCache();
    }

    public function test_returns_db_override_when_set(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'air_purifiers', 'element_type' => 'field', 'element_key' => 'brand_name'],
            ['display_label' => 'Custom Brand Label']
        );

        $result = LabelService::field('air_purifiers', 'brand_name');

        $this->assertEquals('Custom Brand Label', $result);
    }

    public function test_returns_translation_when_no_db_override(): void
    {
        $result = LabelService::field('air_purifiers', 'brand_name');

        $this->assertEquals('Marka', $result);
    }

    public function test_returns_null_when_no_override_and_no_translation(): void
    {
        $result = LabelService::field('air_purifiers', 'nonexistent_field_xyz');

        $this->assertNull($result);
    }

    public function test_db_override_takes_priority_over_translation(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'air_purifiers', 'element_type' => 'field', 'element_key' => 'brand_name'],
            ['display_label' => 'Overridden']
        );

        $result = LabelService::field('air_purifiers', 'brand_name');

        $this->assertEquals('Overridden', $result);
        $this->assertNotEquals('Marka', $result);
    }

    public function test_empty_db_label_falls_through_to_translation(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'air_purifiers', 'element_type' => 'field', 'element_key' => 'brand_name'],
            ['display_label' => '']
        );

        $result = LabelService::field('air_purifiers', 'brand_name');

        $this->assertEquals('Marka', $result);
    }

    public function test_null_db_label_falls_through_to_translation(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'air_purifiers', 'element_type' => 'field', 'element_key' => 'brand_name'],
            ['display_label' => null]
        );

        $result = LabelService::field('air_purifiers', 'brand_name');

        $this->assertEquals('Marka', $result);
    }

    public function test_tab_resolution(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'air_purifiers', 'element_type' => 'tab', 'element_key' => 'basic_info'],
            ['display_label' => 'Custom Tab Name']
        );

        $this->assertEquals('Custom Tab Name', LabelService::tab('air_purifiers', 'basic_info'));
    }

    public function test_section_resolution(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'dehumidifiers', 'element_type' => 'section', 'element_key' => 'parametry_osuszania'],
            ['display_label' => 'Custom Section']
        );

        $this->assertEquals('Custom Section', LabelService::section('dehumidifiers', 'parametry_osuszania'));
    }

    public function test_caches_per_table(): void
    {
        LabelOverride::updateOrCreate(
            ['table_name' => 'sensors', 'element_type' => 'field', 'element_key' => 'model'],
            ['display_label' => 'Cached Label']
        );

        $first = LabelService::field('sensors', 'model');
        $this->assertEquals('Cached Label', $first);

        LabelOverride::where('table_name', 'sensors')
            ->where('element_key', 'model')
            ->update(['display_label' => 'Updated']);

        $second = LabelService::field('sensors', 'model');
        $this->assertEquals('Cached Label', $second);

        LabelService::clearCache();
        $third = LabelService::field('sensors', 'model');
        $this->assertEquals('Updated', $third);
    }

    public function test_translation_files_exist_for_all_products(): void
    {
        $products = [
            'air_purifiers',
            'air_humidifiers',
            'air_conditioners',
            'dehumidifiers',
            'upright_vacuums',
            'sensors',
        ];

        foreach ($products as $product) {
            $this->assertFileExists(
                resource_path("lang/pl/{$product}.php"),
                "Translation file missing for {$product}"
            );

            $translations = require resource_path("lang/pl/{$product}.php");
            $this->assertIsArray($translations);
            $this->assertNotEmpty($translations, "Translation file for {$product} is empty");
            $this->assertArrayHasKey('status', $translations, "{$product} translation should have 'status' key");
            $this->assertArrayHasKey('model', $translations, "{$product} translation should have 'model' key");
        }
    }

    public function test_all_product_translations_have_flat_keys(): void
    {
        $products = ['air_purifiers', 'air_humidifiers', 'air_conditioners', 'dehumidifiers', 'upright_vacuums', 'sensors'];

        foreach ($products as $product) {
            $this->assertEquals('Marka', LabelService::field($product, 'brand_name'), "Failed for {$product}");
        }
    }

    public function test_label_override_model_has_correct_fillable(): void
    {
        $override = new LabelOverride();
        $this->assertEquals(
            ['table_name', 'element_type', 'element_key', 'display_label', 'sort_order'],
            $override->getFillable()
        );
    }

    public function test_gracefully_handles_missing_table(): void
    {
        $result = LabelService::field('nonexistent_table', 'some_field');
        $this->assertNull($result);
    }
}
