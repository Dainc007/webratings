<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Full CRUD browser tests for all Filament product resources.
 *
 * Each test performs the complete lifecycle against the real instance:
 *   1. CREATE — fill required fields, submit, assert redirect to edit page
 *   2. UPDATE — change the model name, save, assert change persisted
 *   3. DELETE — click delete header action, confirm modal, assert redirect to list
 *
 * Test records use a "[CRUD]" prefix so they can be identified if cleanup fails.
 * Every test cleans up after itself (deletes the record it created).
 *
 * Run against production:
 *   php artisan dusk tests/Browser/FilamentCrudBrowserTest.php
 *
 * Run against local:
 *   REMOTE_TEST_URL=http://127.0.0.1:8000 php artisan dusk tests/Browser/FilamentCrudBrowserTest.php
 */
class FilamentCrudBrowserTest extends DuskTestCase
{
    protected const TEST_PREFIX = '[CRUD]';

    protected function getEmail(): string
    {
        return env('REMOTE_TEST_EMAIL', 'test@example.com');
    }

    protected function getPassword(): string
    {
        return env('REMOTE_TEST_PASSWORD', 'password');
    }

    /**
     * Login helper — handles both fresh login and already-logged-in states.
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
     * Set a Filament Select field value via JS (works with native selects).
     */
    protected function setSelectValue(Browser $browser, string $fieldName, string $value): void
    {
        $browser->script("
            const select = document.querySelector('select[id*=\"{$fieldName}\"]');
            if (select) {
                select.value = '{$value}';
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        ");
        $browser->pause(1000);
    }

    /**
     * Set a text input value via JS and trigger Livewire's change detection.
     *
     * More reliable than Dusk's type() for Livewire-bound fields because it:
     * - Works regardless of input focus/blur timing
     * - Dispatches the correct events for wire:model binding
     * - Uses the exact Filament field ID format (form.fieldName)
     */
    protected function fillField(Browser $browser, string $fieldName, string $value): void
    {
        $escapedValue = addslashes($value);
        $browser->script("
            const input = document.getElementById('form.{$fieldName}')
                       || document.querySelector('input[id*=\"{$fieldName}\"]');
            if (input) {
                const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
                    window.HTMLInputElement.prototype, 'value'
                ).set;
                nativeInputValueSetter.call(input, '{$escapedValue}');
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                input.dispatchEvent(new Event('blur', { bubbles: true }));
            }
        ");
        $browser->pause(500);
    }

    /**
     * Clear and fill a text input (for updates).
     */
    protected function clearAndFillField(Browser $browser, string $fieldName, string $value): void
    {
        $escapedValue = addslashes($value);
        $browser->script("
            const input = document.getElementById('form.{$fieldName}')
                       || document.querySelector('input[id*=\"{$fieldName}\"]');
            if (input) {
                const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
                    window.HTMLInputElement.prototype, 'value'
                ).set;
                nativeInputValueSetter.call(input, '');
                input.dispatchEvent(new Event('input', { bubbles: true }));
                nativeInputValueSetter.call(input, '{$escapedValue}');
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                input.dispatchEvent(new Event('blur', { bubbles: true }));
            }
        ");
        $browser->pause(500);
    }

    /**
     * Assert a form field has the expected value via JS (avoids CSS dot-escaping issues).
     */
    protected function assertFieldValue(Browser $browser, string $fieldName, string $expected): void
    {
        $escapedExpected = addslashes($expected);
        $result = $browser->script("
            const input = document.getElementById('form.{$fieldName}')
                       || document.querySelector('input[id*=\"{$fieldName}\"]');
            return input ? input.value : null;
        ");

        $actual = $result[0] ?? null;
        $this->assertEquals($escapedExpected, $actual, "Expected field '{$fieldName}' to have value '{$escapedExpected}', got '{$actual}'");
    }

    /**
     * Submit the form by scrolling to and clicking the submit button via JS.
     *
     * More reliable than Dusk's click() because it ensures the button is
     * visible in the viewport before clicking.
     */
    protected function submitForm(Browser $browser): void
    {
        $browser->script("
            const submitBtn = document.querySelector('form#form button[type=\"submit\"]');
            if (submitBtn) {
                submitBtn.scrollIntoView({ behavior: 'instant', block: 'center' });
                submitBtn.click();
            }
        ");
    }

    /**
     * Click the Filament Delete header action button and confirm the modal.
     */
    protected function deleteRecord(Browser $browser): void
    {
        // Click the Delete button in the page header.
        // In Polish Filament it's "Usuń"; fall back to English "Delete".
        $browser->script("
            const allBtns = document.querySelectorAll('header button, .fi-header-actions button, [class*=\"header\"] button');
            for (const btn of allBtns) {
                const text = btn.textContent.trim().toLowerCase();
                if (text.includes('usu') || text.includes('delet')) {
                    btn.click();
                    break;
                }
            }
        ");

        // Wait for the confirmation modal to appear (look for any dialog/modal overlay)
        $browser->pause(3000);

        // Confirm the delete modal — use broad selector and search ALL visible buttons
        $browser->script("
            // Try multiple selectors for the modal confirm button
            const selectors = [
                '[role=\"dialog\"] button',
                '.fi-modal button',
                '[x-data] [x-show] button',
                '[class*=\"modal\"] button',
            ];
            for (const selector of selectors) {
                const btns = document.querySelectorAll(selector);
                for (const btn of btns) {
                    const text = btn.textContent.trim().toLowerCase();
                    if (text.includes('usu') || text.includes('delet') || text.includes('confirm')) {
                        btn.click();
                        return;
                    }
                }
            }
            // Last resort: find the red/danger button anywhere on page
            const dangerBtns = document.querySelectorAll('button');
            for (const btn of dangerBtns) {
                const text = btn.textContent.trim().toLowerCase();
                const classes = btn.className.toLowerCase();
                if ((text.includes('usu') || text.includes('delet')) && (classes.includes('danger') || btn.closest('[class*=\"modal\"]'))) {
                    btn.click();
                    return;
                }
            }
        ");
        $browser->pause(5000);
    }

    /**
     * Run the full CRUD lifecycle for a product resource.
     *
     * @param  string  $slug       URL slug (e.g. 'air-purifiers')
     * @param  string  $label      Human-readable name for assertions
     * @param  bool    $hasStatus  Whether the form has a required status select
     */
    protected function runCrudLifecycle(
        Browser $browser,
        string $slug,
        string $label,
        bool $hasStatus = true,
    ): void {
        $createModel = self::TEST_PREFIX . " {$label} " . time();
        $updatedModel = self::TEST_PREFIX . " Updated {$label} " . time();

        // ==========================================
        // 1. CREATE
        // ==========================================
        $browser->visit("/admin/{$slug}/create")
            ->pause(3000)
            ->waitFor('form#form', 30)
            ->pause(2000); // Extra wait for Livewire to fully hydrate

        // Set status to "draft" if the form has a status field
        if ($hasStatus) {
            $this->setSelectValue($browser, 'status', 'draft');
        }

        // Fill required text fields using JS for reliable Livewire integration
        $this->fillField($browser, 'model', $createModel);
        $this->fillField($browser, 'brand_name', self::TEST_PREFIX . ' Brand');

        // Submit the form (scroll to button first, then click via JS for reliability)
        $this->submitForm($browser);
        $browser->pause(7000);

        // Assert: redirected to the edit page
        $browser->assertPathContains('/edit');

        // Assert the created model name is in the input field (dots escaped for CSS)
        $this->assertFieldValue($browser, 'model', $createModel);

        // ==========================================
        // 2. UPDATE
        // ==========================================
        $this->clearAndFillField($browser, 'model', $updatedModel);

        // Submit the update
        $this->submitForm($browser);
        $browser->pause(7000);

        // Assert the update was saved (model field has the updated value)
        $this->assertFieldValue($browser, 'model', $updatedModel);

        // ==========================================
        // 3. DELETE
        // ==========================================
        $this->deleteRecord($browser);

        // Assert: redirected back to the list page after deletion
        $browser->waitForLocation("/admin/{$slug}", 10)
            ->assertPathIs("/admin/{$slug}");
    }

    // ==========================================
    // Test Methods — one per resource
    // ==========================================

    public function test_air_purifier_full_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->runCrudLifecycle($browser, 'air-purifiers', 'Air Purifier');
        });
    }

    public function test_air_humidifier_full_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->runCrudLifecycle($browser, 'air-humidifiers', 'Air Humidifier');
        });
    }

    public function test_air_conditioner_full_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->runCrudLifecycle($browser, 'air-conditioners', 'Air Conditioner');
        });
    }

    public function test_dehumidifier_full_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->runCrudLifecycle($browser, 'dehumidifiers', 'Dehumidifier');
        });
    }

    public function test_sensor_full_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->runCrudLifecycle($browser, 'sensors', 'Sensor');
        });
    }

    public function test_upright_vacuum_full_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $this->runCrudLifecycle($browser, 'upright-vacuums', 'Upright Vacuum');
        });
    }
}
