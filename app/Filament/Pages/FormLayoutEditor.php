<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Config\ProductFormStructure;
use App\Enums\CustomFieldStatus;
use App\Enums\Product;
use App\Models\CustomField;
use App\Models\FormLayoutItem;
use App\Models\LabelOverride;
use App\Services\CustomFieldService;
use App\Services\FormLayoutService;
use App\Services\LabelService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;
use UnitEnum;

final class FormLayoutEditor extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static ?string $navigationLabel = 'Edytor układu';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.form-layout-editor';

    public string $selectedTable = '';

    public array $layoutTree = [];

    // ── Add Custom Field modal state ─────────────────────────────────────
    public bool $showAddFieldModal = false;

    public string $newFieldColumnName = '';

    public string $newFieldDisplayName = '';

    public string $newFieldType = 'string';

    public int $targetTabIndex = 0;

    public int $targetSectionIndex = 0;

    public bool $hasPendingFields = false;

    public function mount(): void
    {
        $tables = Product::getValues();
        $this->selectedTable = $tables[0] ?? '';
        $this->loadTree();
    }

    public function updatedSelectedTable(): void
    {
        $this->loadTree();
    }

    public function loadTree(): void
    {
        if ($this->selectedTable === '') {
            $this->layoutTree = [];

            return;
        }

        $structure = FormLayoutService::getStructure($this->selectedTable);

        if (empty($structure)) {
            $this->layoutTree = [];

            return;
        }

        $customFieldKeys = CustomField::where('table_name', $this->selectedTable)
            ->active()
            ->pluck('display_name', 'column_name')
            ->toArray();

        $tree = [];
        foreach ($structure as $tabKey => $tabData) {
            $tabSections = [];
            foreach ($tabData['sections'] ?? [] as $sectionKey => $sectionData) {
                $sectionFields = [];
                foreach ($sectionData['fields'] ?? [] as $fieldKey => $sortOrder) {
                    $isCustom = isset($customFieldKeys[$fieldKey]);
                    $sectionFields[] = [
                        'key'        => $fieldKey,
                        'sort_order' => $sortOrder,
                        'is_custom'  => $isCustom,
                        'display'    => $isCustom ? ($customFieldKeys[$fieldKey] ?? $fieldKey) : (LabelService::field($this->selectedTable, $fieldKey) ?? $fieldKey),
                    ];
                }
                usort($sectionFields, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

                $tabSections[] = [
                    'key'        => $sectionKey,
                    'display'    => LabelService::section($this->selectedTable, $sectionKey),
                    'sort_order' => $sectionData['sort_order'],
                    'fields'     => $sectionFields,
                ];
            }
            usort($tabSections, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

            $tree[] = [
                'key'        => $tabKey,
                'display'    => LabelService::tab($this->selectedTable, $tabKey),
                'sort_order' => $tabData['sort_order'],
                'sections'   => $tabSections,
            ];
        }
        usort($tree, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        $this->layoutTree = $tree;
    }

    // ── Sort operations via wire:sort (update in-memory; user clicks Save to persist) ──

    /** Called by wire:sort on the tabs list. */
    public function sortTabs(string $tabKey, int $position): void
    {
        $tree      = $this->layoutTree;
        $fromIndex = null;

        foreach ($tree as $i => $tab) {
            if ($tab['key'] === $tabKey) {
                $fromIndex = $i;
                break;
            }
        }

        if ($fromIndex === null) {
            return;
        }

        $item = array_splice($tree, $fromIndex, 1)[0];
        array_splice($tree, $position, 0, [$item]);
        $this->layoutTree = array_values($tree);
    }

    /** Called by wire:sort on each tab's sections grid. Item id is "{tabIndex}:{sectionKey}". */
    public function sortSections(string $itemId, int $position): void
    {
        [$tabIndexStr, $sectionKey] = explode(':', $itemId, 2);
        $tabIndex  = (int) $tabIndexStr;
        $fromIndex = null;

        foreach ($this->layoutTree[$tabIndex]['sections'] ?? [] as $i => $section) {
            if ($section['key'] === $sectionKey) {
                $fromIndex = $i;
                break;
            }
        }

        if ($fromIndex === null) {
            return;
        }

        $tree = $this->layoutTree;
        $item = array_splice($tree[$tabIndex]['sections'], $fromIndex, 1)[0];
        array_splice($tree[$tabIndex]['sections'], $position, 0, [$item]);
        $this->layoutTree = $tree;
    }

    /**
     * Called by wire:sort:group on field lists.
     * $destinationId is "{tabIndex}:{sectionIndex}" from wire:sort:group-id.
     * When sorting within the same section $destinationId may be empty.
     */
    public function sortFields(string $fieldKey, int $position, string $destinationId = ''): void
    {
        $fromTabIndex     = null;
        $fromSectionIndex = null;
        $fromFieldIndex   = null;

        foreach ($this->layoutTree as $ti => $tab) {
            foreach ($tab['sections'] as $si => $section) {
                foreach ($section['fields'] as $fi => $field) {
                    if ($field['key'] === $fieldKey) {
                        $fromTabIndex     = $ti;
                        $fromSectionIndex = $si;
                        $fromFieldIndex   = $fi;
                        break 3;
                    }
                }
            }
        }

        if ($fromTabIndex === null) {
            return;
        }

        if ($destinationId === '') {
            $toTabIndex     = $fromTabIndex;
            $toSectionIndex = $fromSectionIndex;
        } else {
            [$toTabStr, $toSectionStr] = explode(':', $destinationId, 2);
            $toTabIndex     = (int) $toTabStr;
            $toSectionIndex = (int) $toSectionStr;
        }

        $tree      = $this->layoutTree;
        $fieldData = array_splice($tree[$fromTabIndex]['sections'][$fromSectionIndex]['fields'], $fromFieldIndex, 1)[0];
        array_splice($tree[$toTabIndex]['sections'][$toSectionIndex]['fields'], $position, 0, [$fieldData]);
        $this->layoutTree = $tree;
    }

    // ── Rename (persists immediately to label_overrides) ─────────────────────

    public function renameTab(int $tabIndex, string $newName): void
    {
        $tab     = $this->layoutTree[$tabIndex] ?? null;
        $newName = trim($newName);

        if ($tab === null || $newName === '' || $newName === $tab['display']) {
            return;
        }

        if (mb_strlen($newName) > ProductFormStructure::MAX_LABEL_LENGTH) {
            Notification::make()->title('Nazwa jest zbyt dluga (max ' . ProductFormStructure::MAX_LABEL_LENGTH . ' znakow)')->danger()->send();

            return;
        }

        if ($newName === $tab['key']) {
            LabelOverride::where([
                'table_name'   => $this->selectedTable,
                'element_type' => 'tab',
                'element_key'  => $tab['key'],
            ])->delete();
        } else {
            LabelOverride::updateOrCreate(
                ['table_name' => $this->selectedTable, 'element_type' => 'tab', 'element_key' => $tab['key']],
                ['display_label' => $newName],
            );
        }

        LabelService::clearCache();
        $this->loadTree();

        Notification::make()->title('Nazwa zakładki zmieniona')->success()->send();
    }

    public function renameSection(int $tabIndex, int $sectionIndex, string $newName): void
    {
        $section = $this->layoutTree[$tabIndex]['sections'][$sectionIndex] ?? null;
        $newName = trim($newName);

        if ($section === null || $newName === '' || $newName === $section['display']) {
            return;
        }

        if (mb_strlen($newName) > ProductFormStructure::MAX_LABEL_LENGTH) {
            Notification::make()->title('Nazwa jest zbyt dluga (max ' . ProductFormStructure::MAX_LABEL_LENGTH . ' znakow)')->danger()->send();

            return;
        }

        if ($newName === $section['key']) {
            LabelOverride::where([
                'table_name'   => $this->selectedTable,
                'element_type' => 'section',
                'element_key'  => $section['key'],
            ])->delete();
        } else {
            LabelOverride::updateOrCreate(
                ['table_name' => $this->selectedTable, 'element_type' => 'section', 'element_key' => $section['key']],
                ['display_label' => $newName],
            );
        }

        LabelService::clearCache();
        $this->loadTree();

        Notification::make()->title('Nazwa sekcji zmieniona')->success()->send();
    }

    public function renameField(int $tabIndex, int $sectionIndex, int $fieldIndex, string $newName): void
    {
        $field   = $this->layoutTree[$tabIndex]['sections'][$sectionIndex]['fields'][$fieldIndex] ?? null;
        $newName = trim($newName);

        if ($field === null || $newName === '' || $newName === $field['display']) {
            return;
        }

        if (mb_strlen($newName) > ProductFormStructure::MAX_LABEL_LENGTH) {
            Notification::make()->title('Nazwa jest zbyt dluga (max ' . ProductFormStructure::MAX_LABEL_LENGTH . ' znakow)')->danger()->send();

            return;
        }

        if ($field['is_custom'] ?? false) {
            CustomField::where('table_name', $this->selectedTable)
                ->where('column_name', $field['key'])
                ->update(['display_name' => $newName]);
        } elseif ($newName === $field['key']) {
            LabelOverride::where([
                'table_name'   => $this->selectedTable,
                'element_type' => 'field',
                'element_key'  => $field['key'],
            ])->delete();
        } else {
            LabelOverride::updateOrCreate(
                ['table_name' => $this->selectedTable, 'element_type' => 'field', 'element_key' => $field['key']],
                ['display_label' => $newName],
            );
        }

        LabelService::clearCache();
        $this->loadTree();

        Notification::make()->title('Nazwa pola zmieniona')->success()->send();
    }

    // ── Add tab / section ───────────────────────────────────────────────────

    public function addTab(): void
    {
        if ($this->selectedTable === '' || empty($this->layoutTree)) {
            return;
        }

        DB::transaction(function (): void {
            $maxSort = FormLayoutItem::where('table_name', $this->selectedTable)
                ->where('element_type', 'tab')
                ->lockForUpdate()
                ->max('sort_order') ?? -1;

            $index = $maxSort + 1;
            $key = 'tab_' . $index;

            while (FormLayoutItem::where('table_name', $this->selectedTable)->where('element_type', 'tab')->where('element_key', $key)->exists()) {
                $index++;
                $key = 'tab_' . $index;
            }

            FormLayoutItem::create([
                'table_name' => $this->selectedTable,
                'element_type' => 'tab',
                'element_key' => $key,
                'parent_key' => null,
                'sort_order' => $maxSort + 1,
            ]);

            $sectionKey = 'section_' . $index;
            FormLayoutItem::create([
                'table_name' => $this->selectedTable,
                'element_type' => 'section',
                'element_key' => $sectionKey,
                'parent_key' => $key,
                'sort_order' => 0,
            ]);
        });

        FormLayoutService::clearCache();
        $this->loadTree();

        Notification::make()->title('Dodano nową zakładkę')->success()->send();
    }

    public function addSection(int $tabIndex): void
    {
        $tab = $this->layoutTree[$tabIndex] ?? null;

        if ($tab === null || $this->selectedTable === '') {
            return;
        }

        DB::transaction(function () use ($tab): void {
            $maxSort = FormLayoutItem::where('table_name', $this->selectedTable)
                ->where('element_type', 'section')
                ->where('parent_key', $tab['key'])
                ->lockForUpdate()
                ->max('sort_order') ?? -1;

            $index = FormLayoutItem::where('table_name', $this->selectedTable)
                ->where('element_type', 'section')
                ->count();

            $key = 'section_' . $index;

            while (FormLayoutItem::where('table_name', $this->selectedTable)->where('element_type', 'section')->where('element_key', $key)->exists()) {
                $index++;
                $key = 'section_' . $index;
            }

            FormLayoutItem::create([
                'table_name' => $this->selectedTable,
                'element_type' => 'section',
                'element_key' => $key,
                'parent_key' => $tab['key'],
                'sort_order' => $maxSort + 1,
            ]);
        });

        FormLayoutService::clearCache();
        $this->loadTree();

        Notification::make()->title('Dodano nową sekcję')->success()->send();
    }

    // ── Custom field management ─────────────────────────────────────────────

    public function openAddFieldModal(int $tabIndex, int $sectionIndex): void
    {
        $this->targetTabIndex = $tabIndex;
        $this->targetSectionIndex = $sectionIndex;
        $this->newFieldColumnName = '';
        $this->newFieldDisplayName = '';
        $this->newFieldType = 'string';
        $this->showAddFieldModal = true;
    }

    public function addCustomField(): void
    {
        $columnName = trim($this->newFieldColumnName);
        $displayName = trim($this->newFieldDisplayName);

        if ($columnName === '' || $displayName === '') {
            Notification::make()->title('Nazwa kolumny i wyświetlana nazwa są wymagane')->danger()->send();

            return;
        }

        if (! preg_match('/^[a-z][a-z0-9_]*$/', $columnName)) {
            Notification::make()->title('Nazwa kolumny moze zawierac tylko male litery, cyfry i podkreslniki')->danger()->send();

            return;
        }

        if (mb_strlen($columnName) > 64) {
            Notification::make()->title('Nazwa kolumny jest zbyt dluga (max 64 znaki)')->danger()->send();

            return;
        }

        if (ProductFormStructure::isReservedColumnName($columnName)) {
            Notification::make()->title("Nazwa \"{$columnName}\" jest zarezerwowanym slowem SQL")->danger()->send();

            return;
        }

        if (DBSchema::hasColumn($this->selectedTable, $columnName)) {
            Notification::make()->title('Kolumna o tej nazwie już istnieje')->danger()->send();

            return;
        }

        // Determine target section key
        $sectionKey = $this->layoutTree[$this->targetTabIndex]['sections'][$this->targetSectionIndex]['key'] ?? null;

        // Create CustomField record
        $customField = CustomField::create([
            'table_name' => $this->selectedTable,
            'column_name' => $columnName,
            'column_type' => $this->newFieldType,
            'display_name' => $displayName,
            'status' => CustomFieldStatus::PENDING,
        ]);

        // Create FormLayoutItem with parent_key pointing to the target section
        $maxSort = FormLayoutItem::where('table_name', $this->selectedTable)
            ->where('element_type', 'field')
            ->where('parent_key', $sectionKey)
            ->max('sort_order') ?? -1;

        FormLayoutItem::updateOrCreate(
            ['table_name' => $this->selectedTable, 'element_type' => 'field', 'element_key' => $columnName],
            ['parent_key' => $sectionKey, 'sort_order' => $maxSort + 1],
        );

        // Dispatch the migration job
        CustomFieldService::createField($customField);

        FormLayoutService::clearCache();

        $this->showAddFieldModal = false;
        $this->hasPendingFields = true;
        $this->loadTree();

        Notification::make()
            ->title("Pole \"{$displayName}\" dodane - migracja w toku")
            ->success()
            ->send();
    }

    public function deleteCustomField(string $fieldKey): void
    {
        $customField = CustomField::where('table_name', $this->selectedTable)
            ->where('column_name', $fieldKey)
            ->active()
            ->first();

        if ($customField === null) {
            Notification::make()->title('To pole nie jest polem niestandardowym lub jest w trakcie przetwarzania')->danger()->send();

            return;
        }

        CustomFieldService::deleteField($customField);

        $this->hasPendingFields = true;
        $this->loadTree();

        Notification::make()->title("Usuwanie pola \"{$fieldKey}\" - migracja w toku")->warning()->send();
    }

    public function checkPendingFields(): void
    {
        $this->hasPendingFields = CustomField::where('table_name', $this->selectedTable)
            ->pending()
            ->exists();

        if (! $this->hasPendingFields) {
            FormLayoutService::clearCache();
            $this->loadTree();
        }
    }

    // ── Seed / Save ───────────────────────────────────────────────────────────

    public function seedLayout(): void
    {
        if ($this->selectedTable === '') {
            return;
        }

        $structure = $this->extractDefaultStructure($this->selectedTable);

        if (empty($structure)) {
            Notification::make()
                ->title('Nie znaleziono domyślnej struktury dla tego produktu')
                ->warning()
                ->send();

            return;
        }

        $count = FormLayoutService::seedDefaultLayout($this->selectedTable, $structure);

        Notification::make()
            ->title("Załadowano domyślny układ ({$count} elementów)")
            ->success()
            ->send();

        $this->loadTree();
    }

    public function saveTree(): void
    {
        $tableName = $this->selectedTable;
        if ($tableName === '') {
            return;
        }

        DB::transaction(function () use ($tableName): void {
            foreach ($this->layoutTree as $tabIndex => $tab) {
                FormLayoutItem::updateOrCreate(
                    ['table_name' => $tableName, 'element_type' => 'tab', 'element_key' => $tab['key']],
                    ['parent_key' => null, 'sort_order' => $tabIndex],
                );

                foreach ($tab['sections'] ?? [] as $sectionIndex => $section) {
                    FormLayoutItem::updateOrCreate(
                        ['table_name' => $tableName, 'element_type' => 'section', 'element_key' => $section['key']],
                        ['parent_key' => $tab['key'], 'sort_order' => $sectionIndex],
                    );

                    foreach ($section['fields'] ?? [] as $fieldIndex => $field) {
                        FormLayoutItem::updateOrCreate(
                            ['table_name' => $tableName, 'element_type' => 'field', 'element_key' => $field['key']],
                            ['parent_key' => $section['key'], 'sort_order' => $fieldIndex],
                        );
                    }
                }
            }
        });

        FormLayoutService::clearCache();

        Notification::make()->title('Układ zapisany')->success()->send();

        $this->loadTree();
    }

    public function getProductOptions(): array
    {
        return Product::getOptions();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * @return array<string, array{sections: array<string, list<string>>}>
     */
    private function extractDefaultStructure(string $tableName): array
    {
        $tabSectionMap = ProductFormStructure::getMap($tableName);

        if (empty($tabSectionMap)) {
            return [];
        }

        $columns = [];
        try {
            $columns = DBSchema::getColumnListing($tableName);
        } catch (\Throwable) {
        }

        $structure = [];
        $assignedFields = [];

        foreach ($tabSectionMap as $tabKey => $sections) {
            $structure[$tabKey] = ['sections' => []];
            foreach ($sections as $sectionKey => $fields) {
                $structure[$tabKey]['sections'][$sectionKey] = $fields;
                $assignedFields = array_merge($assignedFields, $fields);
            }
        }

        $unassigned = array_diff($columns, $assignedFields, ['id', 'created_at', 'updated_at']);
        if (count($unassigned) > 0 && ! empty($structure)) {
            $firstTab = array_key_first($structure);
            $firstSection = array_key_first($structure[$firstTab]['sections'] ?? []);
            if ($firstSection !== null) {
                $structure[$firstTab]['sections'][$firstSection] = array_merge(
                    $structure[$firstTab]['sections'][$firstSection],
                    array_values($unassigned),
                );
            }
        }

        return $structure;
    }
}
