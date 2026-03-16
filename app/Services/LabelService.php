<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LabelOverride;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
        } catch (\Throwable) {
            self::$cache[$tableName] = ['labels' => [], 'sort_orders' => []];
        }

        return self::$cache[$tableName];
    }

    public static function sectionMake(string $tableName, string $key): Section
    {
        return Section::make(self::section($tableName, $key))
            ->headerActions([
                self::makeEditAction($tableName, 'section', $key),
            ]);
    }

    private static function makeEditAction(string $tableName, string $elementType, string $key): Action
    {
        $actionName = 'editLabel_' . md5("{$tableName}_{$elementType}_{$key}");

        return Action::make($actionName)
            ->icon('heroicon-o-pencil-square')
            ->iconButton()
            ->tooltip('Edytuj nazwę')
            ->modalHeading("Edytuj: {$key}")
            ->size('xs')
            ->color('gray')
            ->form([
                TextInput::make('_lo_display_label')
                    ->label('Nowa nazwa')
                    ->placeholder($key),
            ])
            ->fillForm(function () use ($tableName, $elementType, $key): array {
                $override = LabelOverride::where([
                    'table_name' => $tableName,
                    'element_type' => $elementType,
                    'element_key' => $key,
                ])->first();

                return ['_lo_display_label' => $override?->display_label];
            })
            ->action(function (array $data, $livewire) use ($tableName, $elementType, $key): void {
                $label = $data['_lo_display_label'] ?? null;

                if (empty($label)) {
                    LabelOverride::where([
                        'table_name' => $tableName,
                        'element_type' => $elementType,
                        'element_key' => $key,
                    ])->delete();
                } else {
                    LabelOverride::updateOrCreate(
                        [
                            'table_name' => $tableName,
                            'element_type' => $elementType,
                            'element_key' => $key,
                        ],
                        ['display_label' => $label]
                    );
                }

                self::clearCache();

                Notification::make()
                    ->title('Nazwa zaktualizowana')
                    ->success()
                    ->send();

                $livewire->js('setTimeout(() => location.reload(), 500)');
            });
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
