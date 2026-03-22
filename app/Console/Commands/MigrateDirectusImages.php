<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

final class MigrateDirectusImages extends Command
{
    protected $signature = 'directus:migrate-images
        {--dry-run : Show what would be downloaded without actually downloading}
        {--table=* : Only process specific tables (e.g. --table=air_purifiers)}
        {--disk=public : Storage disk to save images to}
        {--token= : Directus API token (required for gallery numeric IDs)}
        {--base-url=https://panel.webratings.pl : Directus base URL}
        {--timeout=30 : HTTP timeout per download in seconds}
        {--gallery : Also process gallery fields (skipped by default, needs --token for numeric IDs)}
        {--overwrite : Re-download and overwrite existing local_image/local_gallery values}';

    protected $description = 'Download images from Directus and save local paths to local_image/local_gallery columns';

    /**
     * Table name => [directory, has_image, has_gallery]
     * Original image/gallery columns are never modified.
     * Downloads are stored in local_image/local_gallery columns.
     */
    private const TABLE_CONFIG = [
        'air_conditioners' => ['dir' => 'air-conditioners', 'image' => true, 'gallery' => true],
        'air_humidifiers'  => ['dir' => 'air-humidifiers', 'image' => true, 'gallery' => true],
        'air_purifiers'    => ['dir' => 'air-purifiers', 'image' => false, 'gallery' => true],
        'dehumidifiers'    => ['dir' => 'dehumidifiers', 'image' => true, 'gallery' => true],
        'sensors'          => ['dir' => 'sensors', 'image' => true, 'gallery' => false],
        'upright_vacuums'  => ['dir' => 'upright-vacuums', 'image' => true, 'gallery' => false],
    ];

    private string $baseUrl;

    private string $disk;

    private ?string $token;

    private int $timeout;

    private bool $dryRun;

    private bool $overwrite;

    private int $downloaded = 0;

    private int $skipped = 0;

    private int $failed = 0;

    public function handle(): int
    {
        $this->baseUrl = rtrim($this->option('base-url'), '/');
        $this->disk = $this->option('disk');
        $this->token = $this->option('token') ?: null;
        $this->timeout = (int) $this->option('timeout');
        $this->dryRun = (bool) $this->option('dry-run');
        $this->overwrite = (bool) $this->option('overwrite');

        $tables = $this->option('table') ?: array_keys(self::TABLE_CONFIG);

        if ($this->dryRun) {
            $this->components->warn('DRY RUN — no files will be downloaded or database records updated.');
        }

        $needsToken = ! ! $this->option('gallery')
            && collect($tables)->contains(fn ($t) => self::TABLE_CONFIG[$t]['gallery'] ?? false);

        if ($needsToken && ! $this->token) {
            $this->components->warn(
                'Gallery fields contain numeric Directus IDs that require authentication. '
                . 'Pass --token=YOUR_TOKEN to download gallery images, or use --skip-gallery to skip them.'
            );
        }

        foreach ($tables as $table) {
            if (! isset(self::TABLE_CONFIG[$table])) {
                $this->components->error("Unknown table: {$table}");

                continue;
            }

            $this->processTable($table, self::TABLE_CONFIG[$table]);
        }

        $this->newLine();
        $this->components->info("Done! Downloaded: {$this->downloaded}, Skipped: {$this->skipped}, Failed: {$this->failed}");

        return $this->failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function processTable(string $table, array $config): void
    {
        $this->components->info("Processing {$table}...");

        $selectColumns = ['id'];
        if ($config['image']) {
            $selectColumns[] = 'image';
            $selectColumns[] = 'local_image';
        }
        if ($config['gallery']) {
            $selectColumns[] = 'gallery';
            $selectColumns[] = 'local_gallery';
        }

        $records = DB::table($table)->select($selectColumns)->get();

        if ($records->isEmpty()) {
            $this->components->warn("  No records in {$table}");

            return;
        }

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $record) {
            $updates = [];

            if ($config['image']) {
                $result = $this->processImageField($record->image, $record->local_image, $config['dir']);
                if ($result !== null) {
                    $updates['local_image'] = $result;
                }
            }

            if ($config['gallery'] && ! ! $this->option('gallery')) {
                $result = $this->processGalleryField($record->gallery, $record->local_gallery, $config['dir']);
                if ($result !== null) {
                    $updates['local_gallery'] = $result;
                }
            }

            if (! empty($updates) && ! $this->dryRun) {
                DB::table($table)->where('id', $record->id)->update($updates);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function processImageField(?string $directusId, ?string $localImage, string $directory): ?string
    {
        if (empty($directusId)) {
            return null;
        }

        // Already has a local path and not overwriting
        if (! empty($localImage) && ! $this->overwrite) {
            $this->skipped++;

            return null;
        }

        if (! $this->isUuid($directusId)) {
            $this->components->warn("  Unexpected image value: {$directusId}");
            $this->skipped++;

            return null;
        }

        return $this->downloadAsset($directusId, $directory);
    }

    private function processGalleryField(mixed $galleryRaw, ?string $localGalleryRaw, string $directory): ?string
    {
        if (empty($galleryRaw)) {
            return null;
        }

        // Already has local gallery and not overwriting
        if (! empty($localGalleryRaw) && ! $this->overwrite) {
            $this->skipped++;

            return null;
        }

        $items = is_string($galleryRaw) ? json_decode($galleryRaw, true) : $galleryRaw;

        if (! is_array($items) || empty($items)) {
            return null;
        }

        $newPaths = [];
        $hasAnyDownload = false;

        foreach ($items as $item) {
            $item = (string) $item;

            if ($this->isUuid($item)) {
                $path = $this->downloadAsset($item, $directory);
                $newPaths[] = $path ?? $item;
                if ($path !== null) {
                    $hasAnyDownload = true;
                }
            } elseif (is_numeric($item)) {
                $path = $this->downloadAsset($item, $directory, isNumericId: true);
                $newPaths[] = $path ?? $item;
                if ($path !== null) {
                    $hasAnyDownload = true;
                }
            } else {
                $newPaths[] = $item;
            }
        }

        return $hasAnyDownload ? json_encode($newPaths) : null;
    }

    private function downloadAsset(string $id, string $directory, bool $isNumericId = false): ?string
    {
        if ($isNumericId && ! $this->token) {
            $this->skipped++;

            return null;
        }

        $url = "{$this->baseUrl}/assets/{$id}?download=";
        if ($this->token) {
            $url .= "&access_token={$this->token}";
        }

        try {
            $headResponse = Http::timeout($this->timeout)->head($url);

            if (! $headResponse->successful()) {
                $this->logFailure($id, $headResponse->status());

                return null;
            }

            $filename = $this->extractFilename($headResponse->header('Content-Disposition'), $id);
            $storagePath = "{$directory}/{$filename}";

            if ($this->dryRun) {
                $size = $headResponse->header('Content-Length', '?');
                $this->components->twoColumnDetail("  {$id}", "{$storagePath} ({$size} bytes)");
                $this->downloaded++;

                return $storagePath;
            }

            // Download the actual file
            $response = Http::timeout($this->timeout)->get($url);

            if (! $response->successful()) {
                $this->logFailure($id, $response->status());

                return null;
            }

            // Handle filename collision — if same name already exists, prefix with short ID
            if (Storage::disk($this->disk)->exists($storagePath)) {
                $shortId = substr($id, 0, 8);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $name = pathinfo($filename, PATHINFO_FILENAME);
                $filename = "{$name}-{$shortId}.{$ext}";
                $storagePath = "{$directory}/{$filename}";
            }

            Storage::disk($this->disk)->put($storagePath, $response->body());
            $this->downloaded++;

            return $storagePath;
        } catch (\Throwable $e) {
            $this->components->error("  Failed {$id}: {$e->getMessage()}");
            $this->failed++;

            return null;
        }
    }

    private function extractFilename(string $header, string $fallbackId): string
    {
        // Try UTF-8 filename first (filename*=UTF-8''encoded)
        if (preg_match("/filename\*=UTF-8''(.+?)(?:;|$)/i", $header, $matches)) {
            return $this->sanitizeFilename(urldecode($matches[1]));
        }

        // Fall back to regular filename
        if (preg_match('/filename="?([^";\n]+)"?/i', $header, $matches)) {
            return $this->sanitizeFilename($matches[1]);
        }

        // Last resort — use the ID
        return "{$fallbackId}.png";
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = trim($filename, " \t\n\r\0\x0B\"");

        // Replace directory separators and null bytes
        $filename = str_replace(['/', '\\', "\0"], '-', $filename);

        // Keep the filename reasonable
        if (mb_strlen($filename) > 200) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = mb_substr(pathinfo($filename, PATHINFO_FILENAME), 0, 190) . '.' . $ext;
        }

        return $filename;
    }

    private function isUuid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value);
    }

    private function logFailure(string $id, int $status): void
    {
        $this->components->error("  Failed to download {$id} (HTTP {$status})");
        $this->failed++;
    }
}
