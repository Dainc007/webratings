<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Enums\CustomFieldStatus;
use App\Jobs\ProcessCustomFieldMigration;
use App\Models\CustomField;
use App\Models\FormLayoutItem;
use App\Models\LabelOverride;
use App\Models\TableColumnPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class ProcessCustomFieldMigrationTest extends TestCase
{
    use RefreshDatabase;

    private CustomField $customField;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customField = CustomField::create([
            'table_name' => 'air_purifiers',
            'column_name' => 'test_job_col',
            'column_type' => 'string',
            'display_name' => 'Test Job Column',
            'status' => CustomFieldStatus::PENDING,
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up any columns we created during tests
        if (Schema::hasColumn('air_purifiers', 'test_job_col')) {
            Schema::table('air_purifiers', function ($table) {
                $table->dropColumn('test_job_col');
            });
        }

        // Clean up generated migration files
        $files = glob(database_path('migrations/*test_job_col*'));
        foreach ($files ?: [] as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    // ── Create Action ────────────────────────────────────────────────────────

    public function test_create_action_adds_column_to_table(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');
        $job->handle();

        $this->assertTrue(Schema::hasColumn('air_purifiers', 'test_job_col'));
    }

    public function test_create_action_sets_status_to_active(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');
        $job->handle();

        $this->customField->refresh();
        $this->assertEquals(CustomFieldStatus::ACTIVE, $this->customField->status);
        $this->assertNull($this->customField->error_message);
    }

    public function test_create_action_saves_migration_file_name(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');
        $job->handle();

        $this->customField->refresh();
        $this->assertNotNull($this->customField->migration_file);
        $this->assertStringContainsString('test_job_col', $this->customField->migration_file);
    }

    public function test_create_action_creates_table_column_preference(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');
        $job->handle();

        $this->assertDatabaseHas('table_column_preferences', [
            'table_name' => 'air_purifiers',
            'column_name' => 'test_job_col',
            'is_visible' => true,
        ]);
    }

    public function test_create_action_creates_form_layout_item(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');
        $job->handle();

        $this->assertDatabaseHas('form_layout_items', [
            'table_name' => 'air_purifiers',
            'element_type' => 'field',
            'element_key' => 'test_job_col',
        ]);
    }

    // ── Delete Action ────────────────────────────────────────────────────────

    public function test_delete_action_removes_column_from_table(): void
    {
        // First create the column
        $createJob = new ProcessCustomFieldMigration($this->customField, 'create');
        $createJob->handle();

        $this->assertTrue(Schema::hasColumn('air_purifiers', 'test_job_col'));

        // Now set status to deleting and run delete job
        $this->customField->refresh();
        $this->customField->update(['status' => CustomFieldStatus::DELETING]);

        $deleteJob = new ProcessCustomFieldMigration($this->customField, 'delete');
        $deleteJob->handle();

        $this->assertFalse(Schema::hasColumn('air_purifiers', 'test_job_col'));
    }

    public function test_delete_action_removes_custom_field_record(): void
    {
        $createJob = new ProcessCustomFieldMigration($this->customField, 'create');
        $createJob->handle();

        $this->customField->refresh();
        $this->customField->update(['status' => CustomFieldStatus::DELETING]);

        $deleteJob = new ProcessCustomFieldMigration($this->customField, 'delete');
        $deleteJob->handle();

        $this->assertDatabaseMissing('custom_fields', [
            'column_name' => 'test_job_col',
        ]);
    }

    public function test_delete_action_cleans_up_related_records(): void
    {
        $createJob = new ProcessCustomFieldMigration($this->customField, 'create');
        $createJob->handle();

        // Add a label override for this field
        LabelOverride::create([
            'table_name' => 'air_purifiers',
            'element_type' => 'field',
            'element_key' => 'test_job_col',
            'display_label' => 'Custom Label',
        ]);

        $this->customField->refresh();
        $this->customField->update(['status' => CustomFieldStatus::DELETING]);

        $deleteJob = new ProcessCustomFieldMigration($this->customField, 'delete');
        $deleteJob->handle();

        $this->assertDatabaseMissing('table_column_preferences', [
            'table_name' => 'air_purifiers',
            'column_name' => 'test_job_col',
        ]);

        $this->assertDatabaseMissing('form_layout_items', [
            'table_name' => 'air_purifiers',
            'element_key' => 'test_job_col',
        ]);

        $this->assertDatabaseMissing('label_overrides', [
            'table_name' => 'air_purifiers',
            'element_key' => 'test_job_col',
        ]);
    }

    // ── Failed Handler ───────────────────────────────────────────────────────

    public function test_failed_sets_status_to_failed_with_error_message(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');
        $exception = new \RuntimeException('Test error message');

        $job->failed($exception);

        $this->customField->refresh();
        $this->assertEquals(CustomFieldStatus::FAILED, $this->customField->status);
        $this->assertEquals('Test error message', $this->customField->error_message);
    }

    // ── Job Configuration ────────────────────────────────────────────────────

    public function test_job_has_correct_configuration(): void
    {
        $job = new ProcessCustomFieldMigration($this->customField, 'create');

        $this->assertEquals(1, $job->tries);
        $this->assertEquals(60, $job->timeout);
    }

    public function test_job_is_queueable(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new ProcessCustomFieldMigration($this->customField, 'create')
        );
    }
}
