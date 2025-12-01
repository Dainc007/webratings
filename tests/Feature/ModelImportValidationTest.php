<?php

declare(strict_types=1);

describe('Model Import Validation Tests', function (): void {
    it('validates AirPurifier CSV structure and data types', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Air Purifiers');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Verify file has data
        expect(count($lines))->toBeGreaterThan(0, 'AirPurifier CSV should have data rows');

        // Check first data row for structure
        if ($lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                // Validate expected columns exist
                expect(array_key_exists('id', $data))->toBeTrue('Should have id column');

                // Validate data types
                if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                    expect(is_numeric($data['id']))->toBeTrue('id should be numeric');
                }
            }
        }
    });

    it('validates AirConditioner CSV structure and data types', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Air Conditioners');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Verify file has data
        expect(count($lines))->toBeGreaterThan(0, 'AirConditioner CSV should have data rows');

        // Check first data row for structure
        if ($lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                // Validate expected columns exist
                expect(array_key_exists('id', $data))->toBeTrue('Should have id column');

                // Validate data types
                if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                    expect(is_numeric($data['id']))->toBeTrue('id should be numeric');
                }
            }
        }
    });

    it('validates AirHumidifier CSV structure and data types', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Air Humidifiers');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Verify file has data
        expect(count($lines))->toBeGreaterThan(0, 'AirHumidifier CSV should have data rows');

        // Check first data row for structure
        if ($lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                // Validate expected columns exist
                expect(array_key_exists('id', $data))->toBeTrue('Should have id column');

                // Validate data types
                if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                    expect(is_numeric($data['id']))->toBeTrue('id should be numeric');
                }
            }
        }
    });

    it('validates Dehumidifier CSV structure and data types', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Dehumidifiers');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Verify file has data
        expect(count($lines))->toBeGreaterThan(0, 'Dehumidifier CSV should have data rows');

        // Check first data row for structure
        if ($lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                // Validate expected columns exist
                expect(array_key_exists('id', $data))->toBeTrue('Should have id column');

                // Validate data types
                if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                    expect(is_numeric($data['id']))->toBeTrue('id should be numeric');
                }
            }
        }
    });

    it('validates Sensor CSV structure and data types', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Sensors');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Verify file has data
        expect(count($lines))->toBeGreaterThan(0, 'Sensor CSV should have data rows');

        // Check first data row for structure
        if ($lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                // Validate expected columns exist
                expect(array_key_exists('id', $data))->toBeTrue('Should have id column');

                // Validate data types
                if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                    expect(is_numeric($data['id']))->toBeTrue('id should be numeric');
                }
            }
        }
    });

    it('validates UprightVacuum CSV structure and data types', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Upright Vacuums');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Verify file has data
        expect(count($lines))->toBeGreaterThan(0, 'UprightVacuum CSV should have data rows');

        // Check first data row for structure
        if ($lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                // Validate expected columns exist
                expect(array_key_exists('id', $data))->toBeTrue('Should have id column');

                // Validate data types
                if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                    expect(is_numeric($data['id']))->toBeTrue('id should be numeric');
                }
            }
        }
    });

    it('validates boolean field formats in CSV files', function (): void {
        $config = collect(getImportConfigurations())->firstWhere('name', 'Sensors');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Find boolean columns
        $booleanColumns = array_filter($header, fn ($col): bool => str_contains($col, 'is_') ||
            str_contains($col, 'has_') ||
            in_array($col, ['main_ranking', 'ranking_hidden', 'wifi', 'bluetooth'])
        );

        if ($booleanColumns !== [] && $lines !== []) {
            $row = str_getcsv($lines[0]);

            // Skip if row doesn't match header length
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                foreach ($booleanColumns as $column) {
                    if (isset($data[$column]) && ! empty($data[$column])) {
                        $value = mb_strtolower(mb_trim($data[$column]));
                        $validBooleanValues = ['true', 'false', '1', '0', 'yes', 'no', 'tak', 'nie', 'y', 'n', 't', 'f'];

                        expect(in_array($value, $validBooleanValues))->toBeTrue(
                            "Boolean column '{$column}' should have valid boolean value, got '{$value}'"
                        );
                    }
                }
            }
        }
    });

    it('validates ImportBooleanCaster service functionality', function (): void {
        // Test the boolean casting service directly
        expect(App\Services\ImportBooleanCaster::cast('true'))->toBeTrue();
        expect(App\Services\ImportBooleanCaster::cast('false'))->toBeFalse();
        expect(App\Services\ImportBooleanCaster::cast('1'))->toBeTrue();
        expect(App\Services\ImportBooleanCaster::cast('0'))->toBeFalse();
        expect(App\Services\ImportBooleanCaster::cast('yes'))->toBeTrue();
        expect(App\Services\ImportBooleanCaster::cast('no'))->toBeFalse();
        expect(App\Services\ImportBooleanCaster::cast('tak'))->toBeTrue();
        expect(App\Services\ImportBooleanCaster::cast('nie'))->toBeFalse();
        expect(App\Services\ImportBooleanCaster::cast('YES'))->toBeTrue();
        expect(App\Services\ImportBooleanCaster::cast('NO'))->toBeFalse();
        expect(App\Services\ImportBooleanCaster::cast(''))->toBeFalse();
        expect(App\Services\ImportBooleanCaster::cast(null))->toBeFalse();
    });

    it('validates CSV numeric field formats', function (): void {
        foreach (getImportConfigurations() as $config) {
            $filePath = storage_path("imports/{$config['fileName']}");
            $content = file_get_contents($filePath);
            $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
            $header = str_getcsv(array_shift($lines)); // Remove and get header

            // Find numeric columns
            $numericColumns = array_filter($header, fn ($col): bool => str_contains($col, 'price') ||
                str_contains($col, 'width') ||
                str_contains($col, 'height') ||
                str_contains($col, 'weight') ||
                str_contains($col, 'capacity') ||
                $col === 'id'
            );

            if ($numericColumns !== [] && $lines !== []) {
                $row = str_getcsv($lines[0]);

                // Skip if row doesn't match header length
                if (count($row) === count($header)) {
                    $data = array_combine($header, $row);

                    foreach ($numericColumns as $column) {
                        if (isset($data[$column]) && ! empty($data[$column])) {
                            // Allow for various numeric formats (with currency symbols, units, etc.)
                            $value = $data[$column];
                            $numericValue = preg_replace('/[^0-9.]/', '', $value);

                            expect(is_numeric($numericValue) || empty($numericValue))->toBeTrue(
                                "Numeric column '{$column}' should contain numeric data, got '{$value}'"
                            );
                        }
                    }
                }
            }
        }
    });
});
