<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\CustomFieldStatus;
use App\Models\CustomField;
use App\Models\FormLayoutItem;
use App\Models\LabelOverride;
use App\Models\TableColumnPreference;
use App\Services\FormLayoutService;
use App\Services\LabelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

final class ProcessCustomFieldMigration implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 60;

    public function __construct(
        public CustomField $customField,
        public string $action,
    ) {}

    public function handle(): void
    {
        match ($this->action) {
            'create' => $this->handleCreate(),
            'delete' => $this->handleDelete(),
        };
    }

    private function handleCreate(): void
    {
        $cf = $this->customField;

        $migrationFile = $this->generateMigration(
            resource_path('stubs/add-column.stub'),
            "add_{$cf->column_name}_to_{$cf->table_name}_table",
            [
                '{{ table }}' => $cf->table_name,
                '{{ column_name }}' => $cf->column_name,
                '{{ column_type }}' => $cf->column_type,
                '{{ nullable }}' => '->nullable()',
            ],
        );

        $cf->update(['migration_file' => $migrationFile]);

        Artisan::call('migrate', ['--force' => true]);

        if (! Schema::hasColumn($cf->table_name, $cf->column_name)) {
            throw new \RuntimeException("Column '{$cf->column_name}' was not created on '{$cf->table_name}' after migration.");
        }

        DB::transaction(function () use ($cf): void {
            TableColumnPreference::firstOrCreate(
                ['table_name' => $cf->table_name, 'column_name' => $cf->column_name],
                ['sort_order' => 0, 'is_visible' => true],
            );

            FormLayoutItem::firstOrCreate(
                ['table_name' => $cf->table_name, 'element_type' => 'field', 'element_key' => $cf->column_name],
                ['parent_key' => null, 'sort_order' => 999],
            );

            $cf->update(['status' => CustomFieldStatus::ACTIVE, 'error_message' => null]);
        });

        FormLayoutService::clearCache();
        LabelService::clearCache();
    }

    private function handleDelete(): void
    {
        $cf = $this->customField;

        $this->generateMigration(
            resource_path('stubs/remove-column.stub'),
            "remove_{$cf->column_name}_from_{$cf->table_name}_table",
            [
                '{{ table }}' => $cf->table_name,
                '{{ column_name }}' => $cf->column_name,
            ],
        );

        Artisan::call('migrate', ['--force' => true]);

        DB::transaction(function () use ($cf): void {
            TableColumnPreference::where(['table_name' => $cf->table_name, 'column_name' => $cf->column_name])->delete();

            FormLayoutItem::where([
                'table_name' => $cf->table_name,
                'element_type' => 'field',
                'element_key' => $cf->column_name,
            ])->delete();

            LabelOverride::where([
                'table_name' => $cf->table_name,
                'element_type' => 'field',
                'element_key' => $cf->column_name,
            ])->delete();

            $cf->delete();
        });

        FormLayoutService::clearCache();
        LabelService::clearCache();
    }

    public function failed(\Throwable $e): void
    {
        $this->customField->update([
            'status' => CustomFieldStatus::FAILED,
            'error_message' => $e->getMessage(),
        ]);
    }

    private function generateMigration(string $stubPath, string $migrationName, array $replacements): string
    {
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$migrationName}.php";
        $path = database_path("migrations/{$fileName}");

        $stub = file_get_contents($stubPath);
        $stub = str_replace(array_keys($replacements), array_values($replacements), $stub);

        File::put($path, $stub);

        return $fileName;
    }
}
