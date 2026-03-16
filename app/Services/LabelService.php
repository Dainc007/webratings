<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LabelOverride;

final class LabelService
{
    /** @var array<string, array<string, array<string, ?string>>> */
    private static array $cache = [];

    /**
     * Priority: DB override -> translation key -> null (Filament auto-label fallback).
     */
    public static function resolve(string $tableName, string $elementType, string $key): ?string
    {
        $overrides = self::loadOverrides($tableName);

        $dbLabel = $overrides[$elementType][$key] ?? null;
        if ($dbLabel !== null && $dbLabel !== '') {
            return $dbLabel;
        }

        $transKey = "{$tableName}.{$key}";
        $translated = __($transKey);

        if ($translated !== $transKey) {
            return $translated;
        }

        return null;
    }

    public static function field(string $tableName, string $key): ?string
    {
        return self::resolve($tableName, 'field', $key);
    }

    public static function tab(string $tableName, string $key): ?string
    {
        return self::resolve($tableName, 'tab', $key);
    }

    public static function section(string $tableName, string $key): ?string
    {
        return self::resolve($tableName, 'section', $key);
    }

    /**
     * @return array<string, array<string, ?string>>
     */
    private static function loadOverrides(string $tableName): array
    {
        if (isset(self::$cache[$tableName])) {
            return self::$cache[$tableName];
        }

        try {
            $overrides = LabelOverride::where('table_name', $tableName)
                ->whereNotNull('display_label')
                ->where('display_label', '!=', '')
                ->get();

            $grouped = [];
            foreach ($overrides as $override) {
                $grouped[$override->element_type][$override->element_key] = $override->display_label;
            }

            self::$cache[$tableName] = $grouped;
        } catch (\Throwable) {
            self::$cache[$tableName] = [];
        }

        return self::$cache[$tableName];
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
