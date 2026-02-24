<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Concerns\HandlesRecordExceptions;
use App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\EditAirHumidifier;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier;
use App\Models\AirHumidifier;
use App\Models\AirPurifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Tests for the HandlesRecordExceptions trait.
 *
 * Verifies that:
 * - All product Create/Edit pages use the trait
 * - Duplicate unique constraint shows a Filament notification, not a crash
 * - Normal create/edit still works with the trait applied
 * - The trait is a no-op when no exceptions occur (zero overhead on happy path)
 */
class HandlesRecordExceptionsTest extends TestCase
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
    // Structural: trait is applied to all pages
    // ==========================================

    #[DataProvider('productPageProvider')]
    public function test_product_page_uses_handles_record_exceptions_trait(string $pageClass): void
    {
        $this->assertTrue(
            in_array(HandlesRecordExceptions::class, class_uses_recursive($pageClass), true),
            "{$pageClass} must use HandlesRecordExceptions trait"
        );
    }

    public static function productPageProvider(): array
    {
        return [
            'CreateAirPurifier' => [\App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier::class],
            'EditAirPurifier' => [\App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier::class],
            'CreateAirHumidifier' => [\App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier::class],
            'EditAirHumidifier' => [\App\Filament\Resources\AirHumidifierResource\Pages\EditAirHumidifier::class],
            'CreateDehumidifier' => [\App\Filament\Resources\DehumidifierResource\Pages\CreateDehumidifier::class],
            'EditDehumidifier' => [\App\Filament\Resources\DehumidifierResource\Pages\EditDehumidifier::class],
            'CreateAirConditioner' => [\App\Filament\Resources\AirConditionerResource\Pages\CreateAirConditioner::class],
            'EditAirConditioner' => [\App\Filament\Resources\AirConditionerResource\Pages\EditAirConditioner::class],
            'CreateSensor' => [\App\Filament\Resources\SensorResource\Pages\CreateSensor::class],
            'EditSensor' => [\App\Filament\Resources\SensorResource\Pages\EditSensor::class],
            'CreateUprightVacuum' => [\App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum::class],
            'EditUprightVacuum' => [\App\Filament\Resources\UprightVacuumResource\Pages\EditUprightVacuum::class],
        ];
    }

    // ==========================================
    // Happy path: trait doesn't break normal flow
    // ==========================================

    public function test_create_still_works_with_trait(): void
    {
        Livewire::test(CreateAirPurifier::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Trait Happy Path ' . time(),
                'brand_name' => 'Test Brand',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('air_purifiers', [
            'brand_name' => 'Test Brand',
        ]);
    }

    public function test_edit_still_works_with_trait(): void
    {
        $record = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Trait Edit Test ' . time(),
            'brand_name' => 'Original',
        ]);

        Livewire::test(EditAirPurifier::class, ['record' => $record->id])
            ->fillForm(['brand_name' => 'Updated'])
            ->call('save')
            ->assertHasNoFormErrors();

        $record->refresh();
        $this->assertEquals('Updated', $record->brand_name);
    }

    // ==========================================
    // Error path: unique violation → notification
    // ==========================================

    /**
     * When a duplicate unique constraint is hit during CREATE,
     * the trait catches the exception and shows a Filament notification
     * instead of crashing the page with a generic Livewire error.
     */
    public function test_create_with_duplicate_unique_shows_notification_not_crash(): void
    {
        AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Original',
            'brand_name' => 'Brand',
            'remote_id' => 999,
        ]);

        $component = Livewire::test(CreateAirHumidifier::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Duplicate',
                'brand_name' => 'Brand',
                'remote_id' => 999,
            ])
            ->call('create');

        // Page did not crash (no 500 / unhandled exception)
        $component->assertSuccessful();

        // Filament notification was sent
        $component->assertNotified('Nie udało się zapisać rekordu');

        // Duplicate was NOT inserted
        $this->assertEquals(1, AirHumidifier::where('remote_id', 999)->count());
    }

    /**
     * When a duplicate unique constraint is hit during EDIT/SAVE,
     * the trait catches the exception and shows a Filament notification.
     */
    public function test_edit_with_duplicate_unique_shows_notification_not_crash(): void
    {
        AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Record A',
            'brand_name' => 'Brand',
            'remote_id' => 111,
        ]);

        $recordB = AirHumidifier::create([
            'status' => 'draft',
            'model' => 'Record B',
            'brand_name' => 'Brand',
            'remote_id' => 222,
        ]);

        $component = Livewire::test(EditAirHumidifier::class, ['record' => $recordB->id])
            ->fillForm(['remote_id' => 111])
            ->call('save');

        $component->assertSuccessful();
        $component->assertNotified('Nie udało się zapisać rekordu');

        // Record B was NOT changed
        $recordB->refresh();
        $this->assertEquals(222, $recordB->remote_id);
    }

    // ==========================================
    // Safety: trait source code review
    // ==========================================

    /**
     * The haltWithNotification method wraps sendErrorNotification in try/catch.
     * Even if notification fails, Halt is still thrown → page stays alive.
     */
    public function test_trait_has_safety_wrapper_around_notification(): void
    {
        $traitSource = file_get_contents(
            app_path('Filament/Concerns/HandlesRecordExceptions.php')
        );

        $this->assertStringContainsString(
            'catch (Throwable)',
            $traitSource,
            'haltWithNotification must catch Throwable around sendErrorNotification'
        );

        $this->assertStringContainsString(
            'throw (new Halt)->rollBackDatabaseTransaction()',
            $traitSource,
            'Must always throw Halt after notification attempt'
        );
    }

    /**
     * fixSequence() only runs on PostgreSQL and has its own try/catch.
     */
    public function test_fix_sequence_is_guarded_by_driver_check(): void
    {
        $traitSource = file_get_contents(
            app_path('Filament/Concerns/HandlesRecordExceptions.php')
        );

        $this->assertStringContainsString(
            "getDriverName() !== 'pgsql'",
            $traitSource,
            'fixSequence must check for PostgreSQL driver before running'
        );
    }
}
