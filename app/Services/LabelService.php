<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LabelOverride;
use Filament\Schemas\Components\Section;

final class LabelService
{
    /** @var array<string, array{labels: array<string, array<string, ?string>>, sort_orders: array<string, array<string, ?int>>}> */
    private static array $cache = [];

    /**
     * Priority: DB override -> translation key -> null (Filament auto-label fallback).
     */
    public static function resolve(string $tableName, string $elementType, string $key): ?string
    {
        $overrides = self::loadOverrides($tableName);

        $dbLabel = $overrides['labels'][$elementType][$key] ?? null;
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

    public static function tab(string $tableName, string $key): string
    {
        return self::resolve($tableName, 'tab', $key) ?? $key;
    }

    public static function section(string $tableName, string $key): string
    {
        return self::resolve($tableName, 'section', $key) ?? $key;
    }

    public static function sortOrder(string $tableName, string $elementType, string $key): ?int
    {
        $overrides = self::loadOverrides($tableName);

        return $overrides['sort_orders'][$elementType][$key] ?? null;
    }

    /**
     * @return array{labels: array<string, array<string, ?string>>, sort_orders: array<string, array<string, ?int>>}
     */
    private static function loadOverrides(string $tableName): array
    {
        if (isset(self::$cache[$tableName])) {
            return self::$cache[$tableName];
        }

        try {
            $overrides = LabelOverride::where('table_name', $tableName)->get();

            $labels = [];
            $sortOrders = [];
            foreach ($overrides as $override) {
                if ($override->display_label !== null && $override->display_label !== '') {
                    $labels[$override->element_type][$override->element_key] = $override->display_label;
                }
                if ($override->sort_order !== null) {
                    $sortOrders[$override->element_type][$override->element_key] = $override->sort_order;
                }
            }

            self::$cache[$tableName] = [
                'labels' => $labels,
                'sort_orders' => $sortOrders,
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('LabelService::loadOverrides failed', [
                'table' => $tableName,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            self::$cache[$tableName] = ['labels' => [], 'sort_orders' => []];
        }

        return self::$cache[$tableName];
    }

    public static function sectionMake(string $tableName, string $key): Section
    {
        return Section::make(self::section($tableName, $key));
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
