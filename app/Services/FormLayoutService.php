<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FormLayoutItem;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

final class FormLayoutService
{
    /** @var array<string, \Illuminate\Support\Collection> */
    private static array $cache = [];

    /**
     * Apply DB layout overrides to an existing array of Tabs.
     *
     * This is the lightweight integration point: resource files keep their existing
     * tab/section/field structure and just wrap the tabs array with this call.
     * If no DB layout exists, the default tabs are returned unchanged.
     *
     * @param  string  $tableName
     * @param  array<int, Tab>  $defaultTabs
     * @return array<int, Tab>
     */
    public static function applyLayout(string $tableName, array $defaultTabs): array
    {
        $layoutItems = self::loadLayout($tableName);

        if ($layoutItems->isEmpty()) {
            return $defaultTabs;
        }

        $fieldMap = self::extractFields($defaultTabs);
        $sectionMap = self::extractSections($defaultTabs);
        $tabMap = self::extractTabMap($defaultTabs);

        $dbTabs = $layoutItems->where('element_type', 'tab')->sortBy('sort_order');
        $dbSections = $layoutItems->where('element_type', 'section');
        $dbFields = $layoutItems->where('element_type', 'field');

        $assignedFieldKeys = $dbFields->pluck('element_key')->toArray();
        $builtTabs = [];

        foreach ($dbTabs as $tabItem) {
            $tabKey = $tabItem->element_key;
            $tabLabel = LabelService::tab($tableName, $tabKey);

            $tabSections = $dbSections
                ->where('parent_key', $tabKey)
                ->sortBy('sort_order');

            $tabSchema = [];

            foreach ($tabSections as $sectionItem) {
                $sectionKey = $sectionItem->element_key;

                $sectionFields = $dbFields
                    ->where('parent_key', $sectionKey)
                    ->sortBy('sort_order');

                $sectionComponents = [];
                foreach ($sectionFields as $fieldItem) {
                    if (isset($fieldMap[$fieldItem->element_key])) {
                        $sectionComponents[] = $fieldMap[$fieldItem->element_key];
                    }
                }

                if (count($sectionComponents) > 0) {
                    if (isset($sectionMap[$sectionKey])) {
                        $section = $sectionMap[$sectionKey];
                        $section->clearCachedDefaultChildSchemas();
                    } else {
                        $section = LabelService::sectionMake($tableName, $sectionKey);
                    }
                    $section->schema($sectionComponents);
                    $tabSchema[] = $section;
                }
            }

            $directFields = $dbFields
                ->where('parent_key', $tabKey)
                ->sortBy('sort_order');

            foreach ($directFields as $fieldItem) {
                if (isset($fieldMap[$fieldItem->element_key])) {
                    $tabSchema[] = $fieldMap[$fieldItem->element_key];
                }
            }

            if (count($tabSchema) > 0) {
                if (isset($tabMap[$tabKey])) {
                    $tab = $tabMap[$tabKey];
                    $tab->clearCachedDefaultChildSchemas();
                } else {
                    $tab = Tab::make($tabLabel);
                }
                $tab->schema($tabSchema);
                $builtTabs[] = $tab;
            }
        }

        $unassigned = array_diff(array_keys($fieldMap), $assignedFieldKeys);
        if (count($unassigned) > 0) {
            $unassignedComponents = [];
            foreach ($unassigned as $key) {
                $unassignedComponents[] = $fieldMap[$key];
            }

            $builtTabs[] = Tab::make('Nieprzypisane')
                ->schema([
                    LabelService::sectionMake($tableName, 'Nieprzypisane')
                        ->schema($unassignedComponents)
                        ->columns(2),
                ])
                ->icon('heroicon-o-exclamation-triangle')
                ->badge(count($unassigned));
        }

        return $builtTabs;
    }

    /**
     * Recursively extract all Field components from tabs into a flat map.
     *
     * @return array<string, Field>
     */
    private static function extractFields(array $tabs): array
    {
        $fields = [];
        foreach ($tabs as $tab) {
            if ($tab instanceof Tab) {
                self::extractFieldsFromComponents($tab->getDefaultChildComponents(), $fields);
            }
        }

        return $fields;
    }

    private static function extractFieldsFromComponents(array $components, array &$fields): void
    {
        foreach ($components as $component) {
            if ($component instanceof Field) {
                $fields[$component->getName()] = $component;
            } elseif (method_exists($component, 'getDefaultChildComponents')) {
                self::extractFieldsFromComponents($component->getDefaultChildComponents(), $fields);
            }
        }
    }

    /**
     * Extract Section components from tabs, keyed by their heading.
     *
     * @return array<string, Section>
     */
    private static function extractSections(array $tabs): array
    {
        $sections = [];
        foreach ($tabs as $tab) {
            if ($tab instanceof Tab) {
                foreach ($tab->getDefaultChildComponents() as $component) {
                    if ($component instanceof Section) {
                        $heading = $component->getHeading();
                        if ($heading !== null) {
                            $sections[$heading] = $component;
                        }
                    }
                }
            }
        }

        return $sections;
    }

    /**
     * Extract Tab components keyed by their label.
     *
     * @return array<string, Tab>
     */
    private static function extractTabMap(array $tabs): array
    {
        $map = [];
        foreach ($tabs as $tab) {
            if ($tab instanceof Tab) {
                $label = $tab->getLabel();
                if ($label !== null) {
                    $map[$label] = $tab;
                }
            }
        }

        return $map;
    }

    /**
     * Seed the default layout for a product from its current hardcoded structure.
     *
     * @param  string  $tableName
     * @param  array<string, array{sections: array<string, list<string>>}>  $structure
     *    e.g. ['Podstawowe informacje' => ['sections' => ['Podstawowe informacje' => ['status', 'model', ...], ...]]]
     */
    public static function seedDefaultLayout(string $tableName, array $structure): int
    {
        $count = 0;
        $tabOrder = 0;

        foreach ($structure as $tabKey => $tabData) {
            FormLayoutItem::updateOrCreate(
                ['table_name' => $tableName, 'element_type' => 'tab', 'element_key' => $tabKey],
                ['parent_key' => null, 'sort_order' => $tabOrder++]
            );
            $count++;

            $sectionOrder = 0;
            foreach ($tabData['sections'] ?? [] as $sectionKey => $fieldKeys) {
                FormLayoutItem::updateOrCreate(
                    ['table_name' => $tableName, 'element_type' => 'section', 'element_key' => $sectionKey],
                    ['parent_key' => $tabKey, 'sort_order' => $sectionOrder++]
                );
                $count++;

                $fieldOrder = 0;
                foreach ($fieldKeys as $fieldKey) {
                    FormLayoutItem::updateOrCreate(
                        ['table_name' => $tableName, 'element_type' => 'field', 'element_key' => $fieldKey],
                        ['parent_key' => $sectionKey, 'sort_order' => $fieldOrder++]
                    );
                    $count++;
                }
            }
        }

        self::clearCache();

        return $count;
    }

    /**
     * Get the current layout for a table as a structured array.
     *
     * @return array<string, array{sort_order: int, sections: array<string, array{sort_order: int, fields: array<string, int>}>}>
     */
    public static function getStructure(string $tableName): array
    {
        $items = self::loadLayout($tableName);
        if ($items->isEmpty()) {
            return [];
        }

        $result = [];
        $tabs = $items->where('element_type', 'tab')->sortBy('sort_order');
        $sections = $items->where('element_type', 'section');
        $fields = $items->where('element_type', 'field');

        foreach ($tabs as $tab) {
            $tabKey = $tab->element_key;
            $result[$tabKey] = [
                'sort_order' => $tab->sort_order,
                'sections' => [],
            ];

            $tabSections = $sections->where('parent_key', $tabKey)->sortBy('sort_order');
            foreach ($tabSections as $section) {
                $sectionKey = $section->element_key;
                $sectionFields = $fields
                    ->where('parent_key', $sectionKey)
                    ->sortBy('sort_order');

                $fieldMap = [];
                foreach ($sectionFields as $field) {
                    $fieldMap[$field->element_key] = $field->sort_order;
                }

                $result[$tabKey]['sections'][$sectionKey] = [
                    'sort_order' => $section->sort_order,
                    'fields' => $fieldMap,
                ];
            }
        }

        return $result;
    }

    private static function loadLayout(string $tableName): \Illuminate\Support\Collection
    {
        if (isset(self::$cache[$tableName])) {
            return self::$cache[$tableName];
        }

        try {
            self::$cache[$tableName] = FormLayoutItem::where('table_name', $tableName)->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('FormLayoutService::loadLayout failed', [
                'table' => $tableName,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            self::$cache[$tableName] = collect();
        }

        return self::$cache[$tableName];
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
