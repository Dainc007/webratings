<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use App\Config\ProductFormStructure;
use PHPUnit\Framework\TestCase;

final class ProductFormStructureTest extends TestCase
{
    public function test_get_map_returns_array_for_known_table(): void
    {
        $map = ProductFormStructure::getMap('air_purifiers');

        $this->assertNotEmpty($map);
        $this->assertArrayHasKey('Podstawowe informacje', $map);
    }

    public function test_get_map_returns_empty_for_unknown_table(): void
    {
        $this->assertEmpty(ProductFormStructure::getMap('nonexistent'));
    }

    public function test_supported_tables_returns_all_products(): void
    {
        $tables = ProductFormStructure::supportedTables();

        $this->assertContains('air_purifiers', $tables);
        $this->assertContains('air_humidifiers', $tables);
        $this->assertContains('air_conditioners', $tables);
        $this->assertContains('dehumidifiers', $tables);
        $this->assertContains('upright_vacuums', $tables);
        $this->assertContains('sensors', $tables);
        $this->assertCount(6, $tables);
    }

    public function test_is_reserved_column_name_detects_sql_keywords(): void
    {
        $this->assertTrue(ProductFormStructure::isReservedColumnName('select'));
        $this->assertTrue(ProductFormStructure::isReservedColumnName('SELECT'));
        $this->assertTrue(ProductFormStructure::isReservedColumnName('from'));
        $this->assertTrue(ProductFormStructure::isReservedColumnName('where'));
        $this->assertTrue(ProductFormStructure::isReservedColumnName('order'));
        $this->assertTrue(ProductFormStructure::isReservedColumnName('table'));
    }

    public function test_is_reserved_column_name_allows_valid_names(): void
    {
        $this->assertFalse(ProductFormStructure::isReservedColumnName('energy_class'));
        $this->assertFalse(ProductFormStructure::isReservedColumnName('max_area'));
        $this->assertFalse(ProductFormStructure::isReservedColumnName('price'));
    }

    public function test_max_label_length_is_defined(): void
    {
        $this->assertIsInt(ProductFormStructure::MAX_LABEL_LENGTH);
        $this->assertGreaterThan(0, ProductFormStructure::MAX_LABEL_LENGTH);
    }

    public function test_each_product_has_at_least_one_tab_with_sections(): void
    {
        foreach (ProductFormStructure::supportedTables() as $table) {
            $map = ProductFormStructure::getMap($table);
            $this->assertNotEmpty($map, "Product {$table} has no tabs defined");

            foreach ($map as $tabKey => $sections) {
                $this->assertNotEmpty($sections, "Tab {$tabKey} in {$table} has no sections");
            }
        }
    }
}
