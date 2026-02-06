<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FormFieldConfiguration;
use App\Models\FormSectionConfiguration;
use App\Models\FormTabConfiguration;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;

final class FormLayoutService
{
    /**
     * Returns an ordered array of tab components based on DB configuration.
     * (Kept for backward compatibility during migration.)
     *
     * @param  string  $tableName  The product table name (e.g. 'air_purifiers')
     * @param  array<string, Tab>  $tabDefinitions  Keyed array of tab_key => Tab component
     * @return array<Tab>
     */
    public static function getOrderedTabs(string $tableName, array $tabDefinitions): array
    {
        $configurations = FormTabConfiguration::where('table_name', $tableName)
            ->orderBy('sort_order')
            ->get();

        if ($configurations->isEmpty()) {
            return array_values($tabDefinitions);
        }

        $orderedTabs = [];

        foreach ($configurations as $config) {
            if (! $config->is_visible) {
                continue;
            }

            if (isset($tabDefinitions[$config->tab_key])) {
                $tab = $tabDefinitions[$config->tab_key];
                $tab->label($config->tab_label);
                $orderedTabs[] = $tab;
            }
        }

        foreach ($tabDefinitions as $key => $tab) {
            if (! $configurations->contains('tab_key', $key)) {
                $orderedTabs[] = $tab;
            }
        }

        return $orderedTabs;
    }

    /**
     * Builds a fully dynamic form layout from DB configuration + field definitions.
     *
     * @param  string  $tableName  The product table name (e.g. 'air_purifiers')
     * @param  array<string, \Closure>  $fieldDefinitions  field_name => Closure returning a Filament component
     * @param  array  $customFieldSchema  Dynamic custom fields from CustomFieldService
     * @return array<Tab>
     */
    public static function buildForm(string $tableName, array $fieldDefinitions, array $customFieldSchema = []): array
    {
        $tabConfigs = FormTabConfiguration::where('table_name', $tableName)
            ->orderBy('sort_order')
            ->get();

        // Fallback: if no tab config exists, render all fields in a single tab
        if ($tabConfigs->isEmpty()) {
            $allComponents = [];
            foreach ($fieldDefinitions as $fieldDef) {
                $allComponents[] = $fieldDef();
            }
            if (count($customFieldSchema) > 0) {
                $allComponents = array_merge($allComponents, $customFieldSchema);
            }

            return [Tab::make('Formularz')->schema($allComponents)];
        }

        $sectionConfigs = FormSectionConfiguration::where('table_name', $tableName)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('tab_key');

        $fieldConfigs = FormFieldConfiguration::where('table_name', $tableName)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('tab_key');

        $tabs = [];

        foreach ($tabConfigs as $tabConfig) {
            if (! $tabConfig->is_visible) {
                continue;
            }

            $tabKey = $tabConfig->tab_key;

            // Special handling for custom_fields tab
            if ($tabKey === 'custom_fields') {
                if (count($customFieldSchema) > 0) {
                    $tab = Tab::make($tabConfig->tab_label)
                        ->schema($customFieldSchema)
                        ->visible(fn () => count($customFieldSchema) > 0);
                    $tabs[] = $tab;
                }

                continue;
            }

            $tabSchema = self::buildTabSchema(
                $tabKey,
                $sectionConfigs->get($tabKey, collect()),
                $fieldConfigs->get($tabKey, collect()),
                $fieldDefinitions,
            );

            $tab = Tab::make($tabConfig->tab_label)->schema($tabSchema);

            if ($tabConfig->columns) {
                $tab->columns($tabConfig->columns);
            }

            $tabs[] = $tab;
        }

        return $tabs;
    }

    /**
     * Builds the schema for a single tab: sections with their fields, plus loose fields.
     */
    private static function buildTabSchema(
        string $tabKey,
        \Illuminate\Support\Collection $sectionConfigs,
        \Illuminate\Support\Collection $fieldConfigs,
        array $fieldDefinitions,
    ): array {
        $schema = [];

        // Track which fields are placed in sections
        $fieldsInSections = [];

        // Group field configs by section_key
        $fieldsBySection = $fieldConfigs->groupBy(fn ($f) => $f->section_key ?? '__loose__');

        // Build sections in order
        foreach ($sectionConfigs as $sectionConfig) {
            if (! $sectionConfig->is_visible) {
                continue;
            }

            $sectionKey = $sectionConfig->section_key;
            $sectionFields = $fieldsBySection->get($sectionKey, collect());

            $sectionComponents = [];
            foreach ($sectionFields->sortBy('sort_order') as $fieldConfig) {
                if (! $fieldConfig->is_visible) {
                    continue;
                }

                $fieldName = $fieldConfig->field_name;
                $fieldsInSections[] = $fieldName;

                if (isset($fieldDefinitions[$fieldName])) {
                    $component = $fieldDefinitions[$fieldName]();
                    $sectionComponents[] = $component;
                }
            }

            if (empty($sectionComponents)) {
                continue;
            }

            $section = Section::make($sectionConfig->section_label)
                ->schema($sectionComponents)
                ->columns($sectionConfig->columns);

            if ($sectionConfig->is_collapsible) {
                $section->collapsible();
            }

            if ($sectionConfig->depends_on) {
                $dependsOn = $sectionConfig->depends_on;
                $section->visible(fn (callable $get) => $get($dependsOn));
            }

            $schema[] = $section;
        }

        // Add loose fields (not in any section) - interleave with sections based on sort_order
        $looseFields = $fieldsBySection->get('__loose__', collect());
        foreach ($looseFields->sortBy('sort_order') as $fieldConfig) {
            if (! $fieldConfig->is_visible) {
                continue;
            }

            $fieldName = $fieldConfig->field_name;
            if (in_array($fieldName, $fieldsInSections, true)) {
                continue;
            }

            if (isset($fieldDefinitions[$fieldName])) {
                $component = $fieldDefinitions[$fieldName]();
                $schema[] = $component;
            }
        }

        return $schema;
    }
}
