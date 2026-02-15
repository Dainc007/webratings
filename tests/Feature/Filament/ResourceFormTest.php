<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\AirConditionerResource\Pages\CreateAirConditioner;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier;
use App\Filament\Resources\DehumidifierResource\Pages\CreateDehumidifier;
use App\Filament\Resources\SensorResource\Pages\CreateSensor;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Models\AirConditioner;
use App\Models\AirPurifier;
use App\Models\Dehumidifier;
use App\Models\Sensor;
use App\Models\UprightVacuum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests for Filament resource form creation and editing.
 *
 * These tests verify that:
 * - Forms can create records with minimal required data
 * - Array fields are properly cast and saved
 * - Nullable fields (remote_id) work correctly
 */
class ResourceFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test creating a Dehumidifier with array fields.
     * This tests the fix for: higrostat cast from boolean to array
     */
    public function test_can_create_dehumidifier_with_array_fields(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Test Model',
                'brand_name' => 'Test Brand',
                'partner_link_rel_2' => ['nofollow', 'sponsored'],
                'ceneo_link_rel_2' => ['nofollow'],
                'modes_of_operation' => ['auto', 'manual'],
                'higrostat' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('dehumidifiers', [
            'model' => 'Test Model',
            'brand_name' => 'Test Brand',
        ]);

        // Verify array fields were saved correctly
        $dehumidifier = Dehumidifier::where('model', 'Test Model')->first();
        $this->assertIsArray($dehumidifier->partner_link_rel_2);
        $this->assertIsArray($dehumidifier->ceneo_link_rel_2);
        $this->assertIsArray($dehumidifier->modes_of_operation);
        $this->assertTrue((bool) $dehumidifier->higrostat);
    }

    /**
     * Test creating a Dehumidifier with empty array fields.
     * This ensures empty arrays don't cause "Array to string conversion" errors.
     */
    public function test_can_create_dehumidifier_with_empty_array_fields(): void
    {
        Livewire::test(CreateDehumidifier::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Empty Arrays Test',
                'brand_name' => 'Test Brand',
                'partner_link_rel_2' => [],
                'ceneo_link_rel_2' => [],
                'modes_of_operation' => [],
                'higrostat' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $dehumidifier = Dehumidifier::where('model', 'Empty Arrays Test')->first();
        $this->assertNotNull($dehumidifier);
    }

    /**
     * Test creating an Air Purifier without remote_id.
     * This tests the fix for: remote_id NOT NULL violation
     */
    public function test_can_create_air_purifier_without_remote_id(): void
    {
        Livewire::test(CreateAirPurifier::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Test Air Purifier',
                'brand_name' => 'Test Brand',
                'price' => 499.99,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('air_purifiers', [
            'model' => 'Test Air Purifier',
            'brand_name' => 'Test Brand',
        ]);

        // Verify remote_id is null (not causing NOT NULL violation)
        $airPurifier = AirPurifier::where('model', 'Test Air Purifier')->first();
        $this->assertNull($airPurifier->remote_id);
    }

    /**
     * Test creating an Air Purifier with array fields (Select multiple).
     * Note: partner_link_rel_2 and ceneo_link_rel_2 use Select::multiple()
     * which properly serializes to JSON arrays.
     */
    public function test_can_create_air_purifier_with_array_fields(): void
    {
        Livewire::test(CreateAirPurifier::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Array Test Purifier',
                'brand_name' => 'Test Brand',
                'partner_link_rel_2' => ['nofollow', 'sponsored'],
                'ceneo_link_rel_2' => ['nofollow'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $airPurifier = AirPurifier::where('model', 'Array Test Purifier')->first();
        $this->assertNotNull($airPurifier);
        $this->assertIsArray($airPurifier->partner_link_rel_2);
        $this->assertIsArray($airPurifier->ceneo_link_rel_2);
    }

    /**
     * Test creating an Air Conditioner without remote_id.
     */
    public function test_can_create_air_conditioner_without_remote_id(): void
    {
        Livewire::test(CreateAirConditioner::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Test AC',
                'brand_name' => 'Test Brand',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('air_conditioners', [
            'model' => 'Test AC',
        ]);

        $ac = AirConditioner::where('model', 'Test AC')->first();
        $this->assertNull($ac->remote_id);
    }

    /**
     * Test creating a Sensor without remote_id.
     */
    public function test_can_create_sensor_without_remote_id(): void
    {
        Livewire::test(CreateSensor::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Test Sensor',
                'brand_name' => 'Test Brand',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sensors', [
            'model' => 'Test Sensor',
        ]);

        $sensor = Sensor::where('model', 'Test Sensor')->first();
        $this->assertNull($sensor->remote_id);
    }

    /**
     * Test creating an Upright Vacuum without remote_id.
     */
    public function test_can_create_upright_vacuum_without_remote_id(): void
    {
        Livewire::test(CreateUprightVacuum::class)
            ->fillForm([
                'status' => 'draft',
                'model' => 'Test Vacuum',
                'brand_name' => 'Test Brand',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('upright_vacuums', [
            'model' => 'Test Vacuum',
        ]);

        $vacuum = UprightVacuum::where('model', 'Test Vacuum')->first();
        $this->assertNull($vacuum->remote_id);
    }

    /**
     * Test editing an Air Purifier.
     */
    public function test_can_edit_air_purifier(): void
    {
        $airPurifier = AirPurifier::create([
            'status' => 'draft',
            'model' => 'Edit Test Purifier',
            'brand_name' => 'Original Brand',
        ]);

        Livewire::test(EditAirPurifier::class, [
            'record' => $airPurifier->id,
        ])
            ->fillForm([
                'brand_name' => 'Updated Brand',
                'price' => 599.99,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $airPurifier->refresh();
        $this->assertEquals('Updated Brand', $airPurifier->brand_name);
        $this->assertEquals(599.99, $airPurifier->price);
    }
}
