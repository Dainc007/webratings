<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Components\FormFieldSearch;
use App\Filament\Resources\AirConditionerResource;
use App\Filament\Resources\AirHumidifierResource;
use App\Filament\Resources\AirPurifierResource;
use App\Filament\Resources\CustomFieldResource;
use App\Filament\Resources\DehumidifierResource;
use App\Filament\Resources\SensorResource;
use App\Filament\Resources\ShortcodeResource;
use App\Filament\Resources\UprightVacuumResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Tests for the FormFieldSearch component.
 *
 * Covers:
 * - Component instantiation and configuration
 * - Presence in all product resource forms (tabbed forms)
 * - Absence in simple settings resource forms (no tabs)
 * - Blade view existence
 *
 * Run with: php artisan test tests/Feature/Filament/FormFieldSearchTest.php
 */
class FormFieldSearchTest extends TestCase
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
    // Component Unit Tests
    // ==========================================

    /**
     * FormFieldSearch::make() returns a properly configured instance.
     */
    public function test_form_field_search_can_be_instantiated(): void
    {
        $component = FormFieldSearch::make();

        $this->assertInstanceOf(FormFieldSearch::class, $component);
    }

    /**
     * The component's Blade view file exists.
     */
    public function test_form_field_search_view_exists(): void
    {
        $viewPath = resource_path('views/filament/components/form-field-search.blade.php');

        $this->assertFileExists($viewPath);
    }

    /**
     * The view contains the Alpine.js search component.
     */
    public function test_form_field_search_view_has_alpine_component(): void
    {
        $viewContent = file_get_contents(resource_path('views/filament/components/form-field-search.blade.php'));

        $this->assertStringContainsString('x-data="formFieldSearch"', $viewContent);
        $this->assertStringContainsString('x-model="query"', $viewContent);
        $this->assertStringContainsString('search()', $viewContent);
    }

    /**
     * The CSS source file exists in resources directory.
     */
    public function test_form_field_search_css_source_exists(): void
    {
        $cssPath = resource_path('css/filament/form-field-search.css');

        $this->assertFileExists($cssPath);
    }

    /**
     * The CSS file is published to public by filament:assets.
     */
    public function test_form_field_search_css_is_published_to_public(): void
    {
        // filament:assets copies to public/css/app/{id}.css by default
        $publicPath = public_path('css/app/form-field-search.css');

        $this->assertFileExists($publicPath);
    }

    /**
     * The CSS file contains dark mode rules using .dark selector.
     */
    public function test_form_field_search_css_has_dark_mode_rules(): void
    {
        $cssContent = file_get_contents(resource_path('css/filament/form-field-search.css'));

        $this->assertStringContainsString('.dark .ffs-input-box', $cssContent);
        $this->assertStringContainsString('.dark .ffs-dropdown', $cssContent);
        $this->assertStringContainsString('.dark .ffs-input', $cssContent);
        $this->assertStringContainsString('.dark .ffs-result-label', $cssContent);
        $this->assertStringContainsString('.dark .ffs-highlight', $cssContent);
    }

    /**
     * The CSS asset is registered in AdminPanelProvider with source path.
     */
    public function test_form_field_search_css_is_registered_in_panel(): void
    {
        $providerContent = file_get_contents(app_path('Providers/Filament/AdminPanelProvider.php'));

        $this->assertStringContainsString("Css::make('form-field-search'", $providerContent);
        $this->assertStringContainsString('resource_path(', $providerContent);
        $this->assertStringContainsString('form-field-search.css', $providerContent);
    }

    /**
     * The Blade view does NOT contain inline <style> blocks (CSS is in dedicated file).
     */
    public function test_form_field_search_view_has_no_inline_styles(): void
    {
        $viewContent = file_get_contents(resource_path('views/filament/components/form-field-search.blade.php'));

        $this->assertStringNotContainsString('<style>', $viewContent);
        $this->assertStringNotContainsString('</style>', $viewContent);
    }

    /**
     * The view contains keyboard navigation support.
     */
    public function test_form_field_search_view_has_keyboard_navigation(): void
    {
        $viewContent = file_get_contents(resource_path('views/filament/components/form-field-search.blade.php'));

        $this->assertStringContainsString('keydown.arrow-down', $viewContent);
        $this->assertStringContainsString('keydown.arrow-up', $viewContent);
        $this->assertStringContainsString('keydown.enter', $viewContent);
        $this->assertStringContainsString('keydown.escape', $viewContent);
    }

    /**
     * The Alpine.js component includes search for tabs, sections, and fields.
     */
    public function test_form_field_search_searches_tabs_sections_and_fields(): void
    {
        $viewContent = file_get_contents(resource_path('views/filament/components/form-field-search.blade.php'));

        // Verify all three search types are present
        $this->assertStringContainsString('.fi-tabs-item', $viewContent, 'Should search tab elements');
        $this->assertStringContainsString('.fi-section-header-heading', $viewContent, 'Should search section headings');
        $this->assertStringContainsString("form.querySelectorAll('label')", $viewContent, 'Should search field labels');
    }

    // ==========================================
    // Resource Integration Tests
    // ==========================================

    /**
     * FormFieldSearch is present in all product resources that use tabbed forms.
     */
    #[DataProvider('productResourceProvider')]
    public function test_product_resource_contains_form_field_search(string $resourceClass, string $resourceFile): void
    {
        $content = file_get_contents(app_path("Filament/Resources/{$resourceFile}"));

        $this->assertStringContainsString(
            'use App\Filament\Components\FormFieldSearch;',
            $content,
            "{$resourceFile}: should import FormFieldSearch"
        );

        $this->assertStringContainsString(
            'FormFieldSearch::make()',
            $content,
            "{$resourceFile}: should use FormFieldSearch::make() in the form"
        );
    }

    /**
     * FormFieldSearch is NOT present in simple settings resources without tabs.
     */
    #[DataProvider('settingsResourceProvider')]
    public function test_settings_resource_does_not_contain_form_field_search(string $resourceClass, string $resourceFile): void
    {
        $content = file_get_contents(app_path("Filament/Resources/{$resourceFile}"));

        $this->assertStringNotContainsString(
            'FormFieldSearch::make()',
            $content,
            "{$resourceFile}: should NOT use FormFieldSearch (simple form without tabs)"
        );
    }

    /**
     * FormFieldSearch appears before the Tabs component in each resource form.
     */
    #[DataProvider('productResourceProvider')]
    public function test_form_field_search_appears_before_tabs(string $resourceClass, string $resourceFile): void
    {
        $content = file_get_contents(app_path("Filament/Resources/{$resourceFile}"));

        $searchPosition = strpos($content, 'FormFieldSearch::make()');
        $tabsPosition = strpos($content, 'Tabs::make(');

        $this->assertNotFalse($searchPosition, "{$resourceFile}: FormFieldSearch::make() not found");
        $this->assertNotFalse($tabsPosition, "{$resourceFile}: Tabs::make() not found");
        $this->assertLessThan(
            $tabsPosition,
            $searchPosition,
            "{$resourceFile}: FormFieldSearch::make() should appear before Tabs::make()"
        );
    }

    // ==========================================
    // Create Page Rendering Tests
    // ==========================================

    /**
     * The create page for AirPurifier renders successfully with FormFieldSearch.
     */
    public function test_air_purifier_create_page_renders_with_form_field_search(): void
    {
        $response = $this->get(AirPurifierResource::getUrl('create'));

        $response->assertSuccessful();
        $response->assertSee('Wpisz nazwę pola którego szukasz...', escape: false);
    }

    /**
     * The create page for AirHumidifier renders successfully with FormFieldSearch.
     */
    public function test_air_humidifier_create_page_renders_with_form_field_search(): void
    {
        $response = $this->get(AirHumidifierResource::getUrl('create'));

        $response->assertSuccessful();
        $response->assertSee('Wpisz nazwę pola którego szukasz...', escape: false);
    }

    /**
     * The create page for Sensor renders successfully with FormFieldSearch.
     */
    public function test_sensor_create_page_renders_with_form_field_search(): void
    {
        $response = $this->get(SensorResource::getUrl('create'));

        $response->assertSuccessful();
        $response->assertSee('Wpisz nazwę pola którego szukasz...', escape: false);
    }

    /**
     * The create page for Dehumidifier renders successfully with FormFieldSearch.
     */
    public function test_dehumidifier_create_page_renders_with_form_field_search(): void
    {
        $response = $this->get(DehumidifierResource::getUrl('create'));

        $response->assertSuccessful();
        $response->assertSee('Wpisz nazwę pola którego szukasz...', escape: false);
    }

    /**
     * The create page for AirConditioner renders successfully with FormFieldSearch.
     */
    public function test_air_conditioner_create_page_renders_with_form_field_search(): void
    {
        $response = $this->get(AirConditionerResource::getUrl('create'));

        $response->assertSuccessful();
        $response->assertSee('Wpisz nazwę pola którego szukasz...', escape: false);
    }

    /**
     * The create page for UprightVacuum renders successfully with FormFieldSearch.
     */
    public function test_upright_vacuum_create_page_renders_with_form_field_search(): void
    {
        $response = $this->get(UprightVacuumResource::getUrl('create'));

        $response->assertSuccessful();
        $response->assertSee('Wpisz nazwę pola którego szukasz...', escape: false);
    }

    // ==========================================
    // Data Providers
    // ==========================================

    public static function productResourceProvider(): array
    {
        return [
            'AirPurifier' => [AirPurifierResource::class, 'AirPurifierResource.php'],
            'AirHumidifier' => [AirHumidifierResource::class, 'AirHumidifierResource.php'],
            'Sensor' => [SensorResource::class, 'SensorResource.php'],
            'Dehumidifier' => [DehumidifierResource::class, 'DehumidifierResource.php'],
            'AirConditioner' => [AirConditionerResource::class, 'AirConditionerResource.php'],
            'UprightVacuum' => [UprightVacuumResource::class, 'UprightVacuumResource.php'],
        ];
    }

    public static function settingsResourceProvider(): array
    {
        return [
            'CustomField' => [CustomFieldResource::class, 'CustomFieldResource.php'],
            'Shortcode' => [ShortcodeResource::class, 'ShortcodeResource.php'],
        ];
    }
}
