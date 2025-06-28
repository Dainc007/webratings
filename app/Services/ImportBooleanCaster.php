<?php

declare(strict_types=1);

namespace App\Services;

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
class ImportBooleanCaster
{
    /**
     * Cast various boolean value formats to proper boolean
     * 
     * Supports: yes/no, tak/nie, true/false, 1/0, y/n, t/f
     * 
     * @param mixed $state
     * @return bool
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
        $falseValues = ['0', 0, 'false', 'no', 'nie', 'n', 'f', false];
        
        $stateLower = is_string($state) ? strtolower(trim($state)) : $state;
        
        if (in_array($stateLower, $trueValues, true)) {
            return true;
        }
        
        if (in_array($stateLower, $falseValues, true)) {
            return false;
        }
        
        return false;
    }
    
    /**
     * Get a closure for use with ImportColumn::castStateUsing()
     * 
     * @return \Closure
     */
    public static function closure(): \Closure
    {
        return function ($state) {
            return static::cast($state);
        };
    }
} 