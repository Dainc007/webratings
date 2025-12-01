<?php

declare(strict_types=1);

namespace App\Services;

use Closure;

/**
 * Service for casting various boolean value formats in CSV imports
 *
 * Usage in ImportColumn:
 *
 * // Simple usage
 * ImportColumn::make('some_boolean_field')
 *     ->castStateUsing(ImportBooleanCaster::closure()),
 *
 * // Or direct usage
 * ImportColumn::make('another_field')
 *     ->castStateUsing(fn($state) => ImportBooleanCaster::cast($state)),
 *
 * Supported input formats:
 * - English: yes/no, true/false, y/n, t/f
 * - Polish: tak/nie
 * - Numeric: 1/0
 * - Boolean: true/false
 * - Empty/null values default to false
 */
final class ImportBooleanCaster
{
    /**
     * Cast various boolean value formats to proper boolean
     *
     * Supports: yes/no, tak/nie, true/false, 1/0, y/n, t/f
     *
     * @param  mixed  $state
     */
    public static function cast($state): bool
    {
        if (is_bool($state)) {
            return $state;
        }

        if (is_null($state) || $state === '') {
            return false;
        }

        $trueValues = ['1', 1, 'true', 'yes', 'tak', 'y', 't', true];

        $stateLower = is_string($state) ? mb_strtolower(mb_trim($state)) : $state;

        return in_array($stateLower, $trueValues, true);
    }

    /**
     * Get a closure for use with ImportColumn::castStateUsing()
     */
    public static function closure(): Closure
    {
        return function ($state): bool {
            return self::cast($state);
        };
    }
}
