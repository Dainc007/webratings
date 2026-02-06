<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser-based tests for form fixes.
 *
 * These tests verify UI interactions for the form field changes:
 * - Toggle reactivity (showing/hiding fields)
 * - Select field options
 * - Field visibility conditions
 *
 * Run with: php artisan dusk tests/Browser/FormFixesBrowserTest.php
 *
 * For local testing, set REMOTE_TEST_URL=http://127.0.0.1:8000 in .env.dusk.local
 * and run: php artisan serve
 */
class FormFixesBrowserTest extends DuskTestCase
{
    protected const TEST_PREFIX = '[FIXES TEST]';

    protected function getEmail(): string
    {
        return env('REMOTE_TEST_EMAIL', 'test@example.com');
    }

    protected function getPassword(): string
    {
        return env('REMOTE_TEST_PASSWORD', 'password');
    }

    /**
     * Login helper
     */
    protected function login(Browser $browser): Browser
    {
        $browser->visit('/admin')
            ->pause(3000);

        $currentUrl = $browser->driver->getCurrentURL();

        if (str_contains($currentUrl, '/login')) {
            $browser->waitFor('form#form', 30)
                ->type('input#form\\.email', $this->getEmail())
                ->type('input#form\\.password', $this->getPassword())
                ->click('form#form button[type="submit"]')
                ->pause(3000);
        }

        return $browser->assertPathBeginsWith('/admin');
    }

    /**
     * Helper to click a toggle
     */
    protected function clickToggle(Browser $browser, string $fieldName): void
    {
        $browser->script("
            const toggle = document.querySelector('button[id=\"form.{$fieldName}\"]');
            if (toggle) {
                toggle.click();
            }
        ");
        $browser->pause(1500);
    }

    /**
     * Helper to check if element is visible
     */
    protected function isElementVisible(Browser $browser, string $selector): bool
    {
        $result = $browser->script("
            const el = document.querySelector('{$selector}');
            return el && el.offsetParent !== null;
        ");

        return $result[0] ?? false;
    }

    /**
     * Helper to select value in Filament Select
     */
    protected function selectFilamentOption(Browser $browser, string $fieldName, string $value): void
    {
        // Click to open the select dropdown
        $browser->click("div[wire\\:key*=\"{$fieldName}\"] button[role=\"combobox\"]")
            ->pause(500);

        // Click the option
        $browser->script("
            const options = document.querySelectorAll('[role=\"option\"]');
            for (const opt of options) {
                if (opt.textContent.includes('{$value}')) {
                    opt.click();
                    break;
                }
            }
        ");
        $browser->pause(1000);
    }

    // ==========================================
    // AIR PURIFIER TESTS
    // ==========================================

    /**
     * Test: Humidification toggle shows/hides related fields
     */
    public function test_air_purifier_humidification_toggle_visibility(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-purifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Humidification tab
            $browser->click('button[id*="humidification"]')
                ->pause(1000);

            // Initially fields should be hidden (toggle off)
            // Click toggle to enable humidification
            $this->clickToggle($browser, 'has_humidification');

            // Now check that humidification_type field appears
            $browser->waitFor('select[id*="humidification_type"]', 10)
                ->assertPresent('select[id*="humidification_type"]');
        });
    }

    /**
     * Test: Hygrostat toggle shows range fields
     */
    public function test_air_purifier_hygrostat_toggle_shows_range(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-purifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Humidification tab
            $browser->click('button[id*="humidification"]')
                ->pause(1000);

            // Click hygrostat toggle
            $this->clickToggle($browser, 'hygrostat');

            // Check that range fields appear
            $browser->waitFor('input[id*="hygrostat_min"]', 10)
                ->assertPresent('input[id*="hygrostat_min"]')
                ->assertPresent('input[id*="hygrostat_max"]');
        });
    }

    /**
     * Test: Gallery section exists in Basic Information
     */
    public function test_air_purifier_has_gallery_in_basic_info(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-purifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Should be on Basic Information tab by default
            // Look for gallery section
            $browser->assertSee('Galeria');
        });
    }

    // ==========================================
    // AIR HUMIDIFIER TESTS
    // ==========================================

    /**
     * Test: Ranking section is in Basic Information
     */
    public function test_air_humidifier_ranking_in_basic_info(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-humidifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Should see Ranking section in first tab
            $browser->assertSee('Ranking');
        });
    }

    /**
     * Test: Type of device is a Select field
     */
    public function test_air_humidifier_type_of_device_is_select(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-humidifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Look for type_of_device select/combobox
            $browser->assertPresent('div[wire\\:key*="type_of_device"]');
        });
    }

    // ==========================================
    // DEHUMIDIFIER TESTS
    // ==========================================

    /**
     * Test: Higrostat is a Toggle (not TagsInput)
     */
    public function test_dehumidifier_higrostat_is_toggle(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/dehumidifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Higrostat tab
            $browser->click('button[id*="higrostat"]')
                ->pause(1000);

            // Should find a toggle button for higrostat
            $browser->assertPresent('button[id*="higrostat"][role="switch"]');
        });
    }

    /**
     * Test: Higrostat toggle shows value fields
     */
    public function test_dehumidifier_higrostat_toggle_shows_fields(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/dehumidifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Higrostat tab
            $browser->click('button[id*="higrostat"]')
                ->pause(1000);

            // Click higrostat toggle
            $this->clickToggle($browser, 'higrostat');

            // Check that value fields appear
            $browser->waitFor('input[id*="min_value_for_hygrostat"]', 10)
                ->assertPresent('input[id*="min_value_for_hygrostat"]')
                ->assertPresent('input[id*="max_value_for_hygrostat"]');
        });
    }

    /**
     * Test: Ranking section in Basic Information
     */
    public function test_dehumidifier_ranking_in_basic_info(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/dehumidifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Should see Oceny i ranking section
            $browser->assertSee('Oceny i ranking');
        });
    }

    // ==========================================
    // AIR CONDITIONER TESTS
    // ==========================================

    /**
     * Test: Type field is a Select with options
     */
    public function test_air_conditioner_type_is_select(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-conditioners/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Look for type select
            $browser->assertPresent('div[wire\\:key*="type"]');

            // Try clicking to see options
            $browser->click('div[wire\\:key*="type"] button[role="combobox"]')
                ->pause(500);

            // Should see predefined options
            $browser->assertSee('Przenośny')
                ->assertSee('Split');
        });
    }

    /**
     * Test: Kategoryzacja tab is removed
     */
    public function test_air_conditioner_no_kategoryzacja_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-conditioners/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Should NOT see Kategoryzacja tab
            $browser->assertDontSee('Kategoryzacja');
        });
    }

    /**
     * Test: Gallery is in first tab
     */
    public function test_air_conditioner_gallery_in_first_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/air-conditioners/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Should see Galeria in first tab
            $browser->assertSee('Galeria');
        });
    }

    // ==========================================
    // UPRIGHT VACUUM TESTS
    // ==========================================

    /**
     * Test: Type is a Select field
     */
    public function test_upright_vacuum_type_is_select(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/upright-vacuums/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Look for type select
            $browser->assertPresent('div[wire\\:key*="type"]');
        });
    }

    /**
     * Test: Power supply affects cable length visibility
     */
    public function test_upright_vacuum_power_supply_cable_visibility(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/upright-vacuums/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Zasilanie tab
            $browser->click('button[id*="zasilanie"]')
                ->pause(1000);

            // Select "Sieciowe" power supply
            $browser->click('div[wire\\:key*="power_supply"] button[role="combobox"]')
                ->pause(500);

            $browser->script("
                const options = document.querySelectorAll('[role=\"option\"]');
                for (const opt of options) {
                    if (opt.textContent.includes('Sieciowe')) {
                        opt.click();
                        break;
                    }
                }
            ");
            $browser->pause(1500);

            // Cable length should now be visible
            $browser->assertPresent('input[id*="cable_length"]');
        });
    }

    /**
     * Test: Mopping function fields are toggles
     */
    public function test_upright_vacuum_mopping_fields_are_toggles(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/upright-vacuums/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Funkcje czyszczenia tab
            $browser->click('button[id*="funkcje"]')
                ->pause(1000);

            // Should find toggle buttons for mopping fields
            $browser->assertPresent('button[id*="mopping_function"][role="switch"]')
                ->assertPresent('button[id*="active_washing_function"][role="switch"]')
                ->assertPresent('button[id*="self_cleaning_function"][role="switch"]');
        });
    }

    /**
     * Test: Type of washing is a multi-select
     */
    public function test_upright_vacuum_type_of_washing_is_select(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/upright-vacuums/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Funkcje czyszczenia tab
            $browser->click('button[id*="funkcje"]')
                ->pause(1000);

            // Look for type_of_washing select
            $browser->assertPresent('div[wire\\:key*="type_of_washing"]');
        });
    }

    /**
     * Test: Battery change is a Select
     */
    public function test_upright_vacuum_battery_change_is_select(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);

            $browser->visit('/admin/upright-vacuums/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Navigate to Zasilanie tab
            $browser->click('button[id*="zasilanie"]')
                ->pause(1000);

            // Look for battery_change select
            $browser->assertPresent('div[wire\\:key*="battery_change"]');
        });
    }

    // ==========================================
    // FULL CRUD TESTS
    // ==========================================

    /**
     * Test: Can create Air Purifier with new fields
     */
    public function test_can_create_air_purifier_with_new_fields(): void
    {
        $testModel = self::TEST_PREFIX . ' Air Purifier ' . time();

        $this->browse(function (Browser $browser) use ($testModel) {
            $this->login($browser);

            $browser->visit('/admin/air-purifiers/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Set status
            $browser->script("
                const select = document.querySelector('select[id*=\"status\"]');
                if (select) {
                    select.value = 'draft';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");
            $browser->pause(500);

            // Fill basic fields
            $browser->type('input[id*="model"]', $testModel)
                ->type('input[id*="brand_name"]', self::TEST_PREFIX . ' Brand')
                ->pause(500);

            // Navigate to Humidification tab and set fields
            $browser->click('button[id*="humidification"]')
                ->pause(1000);

            $this->clickToggle($browser, 'has_humidification');
            $this->clickToggle($browser, 'hygrostat');

            $browser->type('input[id*="hygrostat_min"]', '30')
                ->type('input[id*="hygrostat_max"]', '70')
                ->pause(500);

            // Submit form
            $browser->click('form#form button[type="submit"]')
                ->pause(5000)
                ->assertPathContains('/edit');
        });
    }

    /**
     * Test: Can create Upright Vacuum with new fields
     */
    public function test_can_create_upright_vacuum_with_new_fields(): void
    {
        $testModel = self::TEST_PREFIX . ' Vacuum ' . time();

        $this->browse(function (Browser $browser) use ($testModel) {
            $this->login($browser);

            $browser->visit('/admin/upright-vacuums/create')
                ->pause(3000)
                ->waitFor('form#form', 30);

            // Set status
            $browser->script("
                const select = document.querySelector('select[id*=\"status\"]');
                if (select) {
                    select.value = 'draft';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");
            $browser->pause(500);

            // Fill basic fields
            $browser->type('input[id*="model"]', $testModel)
                ->type('input[id*="brand_name"]', self::TEST_PREFIX . ' Brand')
                ->pause(500);

            // Select type
            $browser->click('div[wire\\:key*="data.type"] button[role="combobox"]')
                ->pause(500);

            $browser->script("
                const options = document.querySelectorAll('[role=\"option\"]');
                for (const opt of options) {
                    if (opt.textContent.includes('Pionowy')) {
                        opt.click();
                        break;
                    }
                }
            ");
            $browser->pause(1000);

            // Submit form
            $browser->click('form#form button[type="submit"]')
                ->pause(5000)
                ->assertPathContains('/edit');
        });
    }
}
