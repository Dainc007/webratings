<?php

declare(strict_types=1);

namespace Tests\Filament;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Smoke tests for Filament admin resource accessibility.
 *
 * These tests verify that all Filament resources are accessible
 * and do not return 500 or 404 errors.
 */
#[Group('filament-remote')]
final class ResourceAccessibilityTest extends FilamentRemoteTestCase
{
    /**
     * Resource configuration for all Filament resources.
     *
     * @return array<string, array{slug: string, name: string}>
     */
    public static function resourceProvider(): array
    {
        return [
            'AirConditioner' => [
                'slug' => 'air-conditioners',
                'name' => 'Air Conditioners',
            ],
            'AirHumidifier' => [
                'slug' => 'air-humidifiers',
                'name' => 'Air Humidifiers',
            ],
            'AirPurifier' => [
                'slug' => 'air-purifiers',
                'name' => 'Air Purifiers',
            ],
            'CustomField' => [
                'slug' => 'custom-fields',
                'name' => 'Custom Fields',
            ],
            'Dehumidifier' => [
                'slug' => 'dehumidifiers',
                'name' => 'Dehumidifiers',
            ],
            'Sensor' => [
                'slug' => 'sensors',
                'name' => 'Sensors',
            ],
            'Shortcode' => [
                'slug' => 'shortcodes',
                'name' => 'Shortcodes',
            ],
            'TableColumnPreference' => [
                'slug' => 'table-column-preferences',
                'name' => 'Table Column Preferences',
            ],
            'UprightVacuum' => [
                'slug' => 'upright-vacuums',
                'name' => 'Upright Vacuums',
            ],
        ];
    }

    /**
     * Test that the admin dashboard is accessible.
     */
    public function test_admin_dashboard_is_accessible(): void
    {
        $response = $this->getUrl('/admin');

        $this->assertNotErrorResponse($response, '/admin');
        $this->assertResponseIs200($response, 'Admin dashboard should be accessible');
    }

    /**
     * Test that the login page is accessible (before authentication).
     */
    public function test_login_page_is_accessible(): void
    {
        // Create a fresh client without authentication
        $response = $this->httpClient->get('/admin/login');

        $this->assertNotErrorResponse($response, '/admin/login');
        $this->assertResponseOk($response, 'Login page should be accessible');
    }

    /**
     * Test that the Air Conditioners list page is accessible.
     */
    public function test_air_conditioners_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/air-conditioners');

        $this->assertNotErrorResponse($response, '/admin/air-conditioners');
        $this->assertResponseIs200($response, 'Air Conditioners list page should return 200');
    }

    /**
     * Test that the Air Conditioners create page is accessible.
     */
    public function test_air_conditioners_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/air-conditioners/create');

        $this->assertNotErrorResponse($response, '/admin/air-conditioners/create');
        $this->assertResponseIs200($response, 'Air Conditioners create page should return 200');
    }

    /**
     * Test that the Air Humidifiers list page is accessible.
     */
    public function test_air_humidifiers_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/air-humidifiers');

        $this->assertNotErrorResponse($response, '/admin/air-humidifiers');
        $this->assertResponseIs200($response, 'Air Humidifiers list page should return 200');
    }

    /**
     * Test that the Air Humidifiers create page is accessible.
     */
    public function test_air_humidifiers_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/air-humidifiers/create');

        $this->assertNotErrorResponse($response, '/admin/air-humidifiers/create');
        $this->assertResponseIs200($response, 'Air Humidifiers create page should return 200');
    }

    /**
     * Test that the Air Purifiers list page is accessible.
     */
    public function test_air_purifiers_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/air-purifiers');

        $this->assertNotErrorResponse($response, '/admin/air-purifiers');
        $this->assertResponseIs200($response, 'Air Purifiers list page should return 200');
    }

    /**
     * Test that the Air Purifiers create page is accessible.
     */
    public function test_air_purifiers_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/air-purifiers/create');

        $this->assertNotErrorResponse($response, '/admin/air-purifiers/create');
        $this->assertResponseIs200($response, 'Air Purifiers create page should return 200');
    }

    /**
     * Test that the Custom Fields list page is accessible.
     */
    public function test_custom_fields_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/custom-fields');

        $this->assertNotErrorResponse($response, '/admin/custom-fields');
        $this->assertResponseIs200($response, 'Custom Fields list page should return 200');
    }

    /**
     * Test that the Custom Fields create page is accessible.
     */
    public function test_custom_fields_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/custom-fields/create');

        $this->assertNotErrorResponse($response, '/admin/custom-fields/create');
        $this->assertResponseIs200($response, 'Custom Fields create page should return 200');
    }

    /**
     * Test that the Dehumidifiers list page is accessible.
     */
    public function test_dehumidifiers_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/dehumidifiers');

        $this->assertNotErrorResponse($response, '/admin/dehumidifiers');
        $this->assertResponseIs200($response, 'Dehumidifiers list page should return 200');
    }

    /**
     * Test that the Dehumidifiers create page is accessible.
     */
    public function test_dehumidifiers_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/dehumidifiers/create');

        $this->assertNotErrorResponse($response, '/admin/dehumidifiers/create');
        $this->assertResponseIs200($response, 'Dehumidifiers create page should return 200');
    }

    /**
     * Test that the Sensors list page is accessible.
     */
    public function test_sensors_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/sensors');

        $this->assertNotErrorResponse($response, '/admin/sensors');
        $this->assertResponseIs200($response, 'Sensors list page should return 200');
    }

    /**
     * Test that the Sensors create page is accessible.
     */
    public function test_sensors_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/sensors/create');

        $this->assertNotErrorResponse($response, '/admin/sensors/create');
        $this->assertResponseIs200($response, 'Sensors create page should return 200');
    }

    /**
     * Test that the Shortcodes list page is accessible.
     */
    public function test_shortcodes_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/shortcodes');

        $this->assertNotErrorResponse($response, '/admin/shortcodes');
        $this->assertResponseIs200($response, 'Shortcodes list page should return 200');
    }

    /**
     * Test that the Shortcodes create page is accessible.
     */
    public function test_shortcodes_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/shortcodes/create');

        $this->assertNotErrorResponse($response, '/admin/shortcodes/create');
        $this->assertResponseIs200($response, 'Shortcodes create page should return 200');
    }

    /**
     * Test that the Table Column Preferences list page is accessible.
     */
    public function test_table_column_preferences_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/table-column-preferences');

        $this->assertNotErrorResponse($response, '/admin/table-column-preferences');
        $this->assertResponseIs200($response, 'Table Column Preferences list page should return 200');
    }

    /**
     * Test that the Table Column Preferences create page is accessible.
     */
    public function test_table_column_preferences_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/table-column-preferences/create');

        $this->assertNotErrorResponse($response, '/admin/table-column-preferences/create');
        $this->assertResponseIs200($response, 'Table Column Preferences create page should return 200');
    }

    /**
     * Test that the Upright Vacuums list page is accessible.
     */
    public function test_upright_vacuums_list_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/upright-vacuums');

        $this->assertNotErrorResponse($response, '/admin/upright-vacuums');
        $this->assertResponseIs200($response, 'Upright Vacuums list page should return 200');
    }

    /**
     * Test that the Upright Vacuums create page is accessible.
     */
    public function test_upright_vacuums_create_page_is_accessible(): void
    {
        $response = $this->getUrl('/admin/upright-vacuums/create');

        $this->assertNotErrorResponse($response, '/admin/upright-vacuums/create');
        $this->assertResponseIs200($response, 'Upright Vacuums create page should return 200');
    }

    /**
     * Test all resource list pages in a single test using data provider pattern.
     * This is useful for quick smoke testing of all resources.
     */
    #[DataProvider('resourceProvider')]
    public function test_resource_list_page_is_accessible(string $slug, string $name): void
    {
        $url = "/admin/{$slug}";
        $response = $this->getUrl($url);

        $this->assertNotErrorResponse($response, $url);
        $this->assertResponseIs200($response, "{$name} list page should return 200");
    }

    /**
     * Test all resource create pages in a single test using data provider pattern.
     */
    #[DataProvider('resourceProvider')]
    public function test_resource_create_page_is_accessible(string $slug, string $name): void
    {
        $url = "/admin/{$slug}/create";
        $response = $this->getUrl($url);

        $this->assertNotErrorResponse($response, $url);
        $this->assertResponseIs200($response, "{$name} create page should return 200");
    }
}
