<?php

declare(strict_types=1);

describe('Import Tests', function (): void {
    it('imports all CSV files and model counts match row counts', function (): void {
        foreach (getImportConfigurations() as $config) {
            $expectedRowCount = getImportFileRowCount($config['fileName']);
            $filePath = storage_path("imports/{$config['fileName']}");

            // Read and parse the CSV file manually
            $content = file_get_contents($filePath);
            $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
            $header = str_getcsv(array_shift($lines)); // Remove header

            $importedCount = 0;
            $failedRows = [];
            $malformedRows = [];

            // Process each data row
            foreach ($lines as $lineIndex => $line) {
                $rowNumber = $lineIndex + 2; // +2 because we removed header and arrays are 0-indexed
                $row = str_getcsv($line);

                // Check for malformed rows
                if (count($row) !== count($header)) {
                    $malformedRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Column count mismatch: expected '.count($header).' columns, got '.count($row),
                        'data' => implode('|', $row),
                    ];

                    continue;
                }

                $data = array_combine($header, $row);

                // Create a model record directly (simulating import)
                $modelClass = $config['modelClass'];
                $record = new $modelClass();

                // Set basic required fields
                if (isset($data['id'])) {
                    $record->remote_id = $data['id']; // Map CSV 'id' to model 'remote_id'
                }
                if (isset($data['status'])) {
                    $record->status = $data['status'];
                }
                if (isset($data['model'])) {
                    $record->model = $data['model'];
                }
                if (isset($data['brand_name'])) {
                    $record->brand_name = $data['brand_name'];
                }

                // Save the record
                try {
                    $record->save();
                    $importedCount++;
                } catch (Exception $e) {
                    $failedRows[] = [
                        'row' => $rowNumber,
                        'reason' => $e->getMessage(),
                        'data' => $data,
                    ];
                }
            }

            // Display import results
            echo "\n{$config['name']}: Imported {$importedCount} records from {$expectedRowCount} CSV rows ({$config['fileName']})";

            // Display failed rows if any
            if ($malformedRows !== []) {
                echo "\n  MALFORMED ROWS:";
                foreach ($malformedRows as $failed) {
                    echo "\n    Row {$failed['row']}: {$failed['reason']}";
                    echo "\n      Data: {$failed['data']}";
                }
            }

            if ($failedRows !== []) {
                echo "\n  FAILED ROWS:";
                foreach ($failedRows as $failed) {
                    echo "\n    Row {$failed['row']}: {$failed['reason']}";
                    echo "\n      Data: ".json_encode($failed['data'], JSON_UNESCAPED_UNICODE);
                }
            }

            // Verify that the number of imported records matches expected row count
            $actualDbCount = $config['modelClass']::count();
            expect($actualDbCount)->toBe($importedCount,
                "Expected {$importedCount} {$config['modelClass']} records to be imported, but database has {$actualDbCount}");

            // REQUIRE 100% IMPORT SUCCESS - TEST SHOULD FAIL IF ANY ROWS ARE NOT IMPORTED
            $totalFailedRows = count($malformedRows) + count($failedRows);
            expect($importedCount)->toBe($expectedRowCount,
                "IMPORT FAILED: Expected 100% import success ({$expectedRowCount} records), but only imported {$importedCount} records. {$totalFailedRows} rows failed.");
        }
    });

    it('validates all CSV files exist and can be read', function (): void {
        foreach (getImportConfigurations() as $config) {
            $filePath = storage_path("imports/{$config['fileName']}");

            // Check file exists
            expect(file_exists($filePath))->toBeTrue("Import file {$config['fileName']} should exist");

            // Check file is readable
            expect(is_readable($filePath))->toBeTrue("Import file {$config['fileName']} should be readable");

            // Check file has content
            $content = file_get_contents($filePath);
            expect(mb_strlen($content))->toBeGreaterThan(0, "Import file {$config['fileName']} should not be empty");

            // Check file has proper CSV structure
            $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
            expect(count($lines))->toBeGreaterThan(1, "Import file {$config['fileName']} should have header and data rows");

            // Check header row exists
            $header = str_getcsv($lines[0]);
            expect(count($header))->toBeGreaterThan(0, "Import file {$config['fileName']} should have header columns");
        }
    });

    it('validates CSV row counts match expected values', function (): void {
        foreach (getImportConfigurations() as $config) {
            $expectedRowCount = getImportFileRowCount($config['fileName']);
            $filePath = storage_path("imports/{$config['fileName']}");

            // Read file and count data rows (excluding header)
            $content = file_get_contents($filePath);
            $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
            $dataRowCount = count($lines) - 1; // Subtract header row

            expect($dataRowCount)->toBe($expectedRowCount,
                "Expected {$expectedRowCount} data rows in {$config['fileName']}, but found {$dataRowCount}");
        }
    });

    it('validates CSV headers contain required columns', function (): void {
        foreach (getImportConfigurations() as $config) {
            $filePath = storage_path("imports/{$config['fileName']}");
            $content = file_get_contents($filePath);
            $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
            $header = str_getcsv($lines[0]);

            // Check for common required columns (use 'id' instead of 'remote_id')
            $requiredColumns = ['id'];
            foreach ($requiredColumns as $column) {
                expect(in_array($column, $header))->toBeTrue(
                    "Import file {$config['fileName']} should contain required column '{$column}'"
                );
            }
        }
    });

    it('validates CSV data integrity', function (): void {
        foreach (getImportConfigurations() as $config) {
            $filePath = storage_path("imports/{$config['fileName']}");
            $content = file_get_contents($filePath);
            $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
            $header = str_getcsv(array_shift($lines)); // Remove and get header

            // Check first few data rows for basic integrity
            $sampleSize = min(3, count($lines));
            for ($i = 0; $i < $sampleSize; $i++) {
                if (isset($lines[$i])) {
                    $row = str_getcsv($lines[$i]);

                    // Skip rows that don't match header length (malformed CSV)
                    if (count($row) !== count($header)) {
                        continue;
                    }

                    $data = array_combine($header, $row);

                    // Check id is numeric if present (using 'id' instead of 'remote_id')
                    if (isset($data['id']) && (isset($data['id']) && ($data['id'] !== '' && $data['id'] !== '0'))) {
                        expect(is_numeric($data['id']))->toBeTrue(
                            "id should be numeric in {$config['fileName']} row ".($i + 2)
                        );
                    }

                    // Check status is valid if present
                    if (isset($data['status']) && (isset($data['status']) && ($data['status'] !== '' && $data['status'] !== '0'))) {
                        expect($data['status'])->toBeIn(['draft', 'published', 'archived'],
                            "status should be valid in {$config['fileName']} row ".($i + 2)
                        );
                    }
                }
            }
        }
    });

    it('validates that models exist for each importer', function (): void {
        foreach (getImportConfigurations() as $config) {
            // Check that the model class exists
            expect(class_exists($config['modelClass']))->toBeTrue(
                "Model class {$config['modelClass']} should exist"
            );

            // Check that the importer class exists
            expect(class_exists($config['importerClass']))->toBeTrue(
                "Importer class {$config['importerClass']} should exist"
            );
        }
    });

    it('validates boolean field formats in CSV files', function (): void {
        // Check sensor CSV for boolean field formats
        $config = collect(getImportConfigurations())->firstWhere('name', 'Sensors');
        $filePath = storage_path("imports/{$config['fileName']}");
        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content), fn ($line): bool => ! in_array(mb_trim($line), ['', '0'], true));
        $header = str_getcsv(array_shift($lines)); // Remove and get header

        // Check if boolean columns exist and have valid values
        $booleanColumns = array_filter($header, fn ($col): bool => str_contains($col, 'is_') ||
            str_contains($col, 'has_') ||
            in_array($col, ['main_ranking', 'ranking_hidden', 'wifi', 'bluetooth'])
        );

        if ($booleanColumns !== []) {
            // Check first few rows for boolean value formats
            $sampleSize = min(3, count($lines));
            for ($i = 0; $i < $sampleSize; $i++) {
                if (isset($lines[$i])) {
                    $row = str_getcsv($lines[$i]);

                    // Skip rows that don't match header length (malformed CSV)
                    if (count($row) !== count($header)) {
                        continue;
                    }

                    $data = array_combine($header, $row);

                    foreach ($booleanColumns as $column) {
                        if (isset($data[$column]) && ! empty($data[$column])) {
                            $value = mb_strtolower(mb_trim($data[$column]));
                            $validBooleanValues = ['true', 'false', '1', '0', 'yes', 'no', 'tak', 'nie', 'y', 'n', 't', 'f'];

                            expect(in_array($value, $validBooleanValues))->toBeTrue(
                                "Boolean column '{$column}' should have valid boolean value, got '{$value}' in row ".($i + 2)
                            );
                        }
                    }
                }
            }
        }
    });
});
