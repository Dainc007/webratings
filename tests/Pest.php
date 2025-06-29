<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

uses(RefreshDatabase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getImportFileRowCount(string $fileName): int
{
    $filePath = storage_path("imports/{$fileName}");

    if (! file_exists($filePath)) {
        throw new InvalidArgumentException("Import file not found: {$filePath}");
    }

    $content = file_get_contents($filePath);
    $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(trim($line), ['', '0'], true));

    // Subtract 1 for header row
    return count($lines) - 1;
}

function getImportConfigurations(): array
{
    return [
        [
            'name' => 'Air Purifiers',
            'fileName' => 'airpurifiers.csv',
            'importerClass' => App\Filament\Imports\AirPurifierImporter::class,
            'modelClass' => App\Models\AirPurifier::class,
        ],
        [
            'name' => 'Air Conditioners',
            'fileName' => 'airconditioners.csv',
            'importerClass' => App\Filament\Imports\AirConditionerImporter::class,
            'modelClass' => App\Models\AirConditioner::class,
        ],
        [
            'name' => 'Air Humidifiers',
            'fileName' => 'airhumidifiers.csv',
            'importerClass' => App\Filament\Imports\AirHumidifierImporter::class,
            'modelClass' => App\Models\AirHumidifier::class,
        ],
        [
            'name' => 'Dehumidifiers',
            'fileName' => 'dehumidifiers.csv',
            'importerClass' => App\Filament\Imports\DehumidifierImporter::class,
            'modelClass' => App\Models\Dehumidifier::class,
        ],
        [
            'name' => 'Sensors',
            'fileName' => 'sensors.csv',
            'importerClass' => App\Filament\Imports\SensorImporter::class,
            'modelClass' => App\Models\Sensor::class,
        ],
        [
            'name' => 'Upright Vacuums',
            'fileName' => 'uprightvacuums.csv',
            'importerClass' => App\Filament\Imports\UprightVacuumImporter::class,
            'modelClass' => App\Models\UprightVacuum::class,
        ],
    ];
}

function createImportFromFile(string $fileName, string $importerClass): Filament\Actions\Imports\Models\Import
{
    $filePath = storage_path("imports/{$fileName}");
    $expectedRows = getImportFileRowCount($fileName);

    $content = file_get_contents($filePath);
    $tempFile = Illuminate\Http\UploadedFile::fake()->createWithContent($fileName, $content);

    // Create a test user for the import
    $user = App\Models\User::factory()->create();

    return Filament\Actions\Imports\Models\Import::create([
        'completed_at' => null,
        'file_name' => $fileName,
        'file_path' => $tempFile->store('imports'),
        'importer' => $importerClass,
        'processed_rows' => 0,
        'total_rows' => $expectedRows,
        'successful_rows' => 0,
        'user_id' => $user->id,
    ]);
}
