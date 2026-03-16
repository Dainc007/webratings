<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\Product;
use App\Models\FormLayoutItem;
use App\Services\FormLayoutService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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

    /** @var array<string, mixed> */
    public array $layoutTree = [];

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

        $tree = [];
        foreach ($structure as $tabKey => $tabData) {
            $tabSections = [];
            foreach ($tabData['sections'] ?? [] as $sectionKey => $sectionData) {
                $sectionFields = [];
                foreach ($sectionData['fields'] ?? [] as $fieldKey => $sortOrder) {
                    $sectionFields[] = [
                        'key' => $fieldKey,
                        'sort_order' => $sortOrder,
                    ];
                }
                usort($sectionFields, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

                $tabSections[] = [
                    'key' => $sectionKey,
                    'sort_order' => $sectionData['sort_order'],
                    'fields' => $sectionFields,
                ];
            }
            usort($tabSections, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

            $tree[] = [
                'key' => $tabKey,
                'sort_order' => $tabData['sort_order'],
                'sections' => $tabSections,
            ];
        }
        usort($tree, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        $this->layoutTree = $tree;
    }

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

    /**
     * Save the reordered tree back to the database.
     *
     * @param  array<int, array{key: string, sections: array<int, array{key: string, fields: array<int, array{key: string}>}>}>  $tree
     */
    public function saveTree(array $tree): void
    {
        $tableName = $this->selectedTable;
        if ($tableName === '') {
            return;
        }

        foreach ($tree as $tabIndex => $tab) {
            FormLayoutItem::updateOrCreate(
                ['table_name' => $tableName, 'element_type' => 'tab', 'element_key' => $tab['key']],
                ['parent_key' => null, 'sort_order' => $tabIndex]
            );

            foreach ($tab['sections'] ?? [] as $sectionIndex => $section) {
                FormLayoutItem::updateOrCreate(
                    ['table_name' => $tableName, 'element_type' => 'section', 'element_key' => $section['key']],
                    ['parent_key' => $tab['key'], 'sort_order' => $sectionIndex]
                );

                foreach ($section['fields'] ?? [] as $fieldIndex => $field) {
                    FormLayoutItem::updateOrCreate(
                        ['table_name' => $tableName, 'element_type' => 'field', 'element_key' => $field['key']],
                        ['parent_key' => $section['key'], 'sort_order' => $fieldIndex]
                    );
                }
            }
        }

        FormLayoutService::clearCache();

        Notification::make()
            ->title('Układ zapisany')
            ->success()
            ->send();

        $this->loadTree();
    }

    public function getProductOptions(): array
    {
        return Product::getOptions();
    }

    /**
     * Extract the default form structure from a resource file by inspecting the hardcoded
     * tab/section/field map in LabelOverrideResource.
     *
     * @return array<string, array{sections: array<string, list<string>>}>
     */
    private function extractDefaultStructure(string $tableName): array
    {
        $resourceMap = [
            'air_purifiers' => \App\Filament\Resources\AirPurifierResource::class,
            'air_humidifiers' => \App\Filament\Resources\AirHumidifierResource::class,
            'air_conditioners' => \App\Filament\Resources\AirConditionerResource::class,
            'dehumidifiers' => \App\Filament\Resources\DehumidifierResource::class,
            'upright_vacuums' => \App\Filament\Resources\UprightVacuumResource::class,
            'sensors' => \App\Filament\Resources\SensorResource::class,
        ];

        if (! isset($resourceMap[$tableName])) {
            return [];
        }

        $columns = [];
        try {
            $columns = DBSchema::getColumnListing($tableName);
        } catch (\Throwable) {
        }

        $tabSectionMap = $this->getTabSectionMap($tableName);

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
                    array_values($unassigned)
                );
            }
        }

        return $structure;
    }

    /**
     * @return array<string, array<string, list<string>>>
     */
    private function getTabSectionMap(string $tableName): array
    {
        $maps = [
            'air_purifiers' => [
                'Podstawowe informacje' => [
                    'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                    'Oceny i ranking' => ['capability_points', 'profitability_points', 'popularity'],
                    'Linki partnerskie' => ['partner_link_url', 'partner_link_rel_2'],
                    'Ceneo' => ['ceneo_url', 'ceneo_link_rel_2'],
                    'Galeria' => ['gallery'],
                ],
                'Wydajność' => [
                    'Wydajność' => ['max_performance', 'max_area', 'max_area_ro', 'number_of_fan_speeds', 'min_loudness', 'max_loudness', 'min_rated_power_consumption', 'max_rated_power_consumption'],
                ],
                'Nawilżanie' => [
                    'Nawilżanie' => ['has_humidification', 'humidification_type', 'humidification_switch', 'humidification_area', 'water_tank_capacity', 'humidification_efficiency'],
                    'Higrostat' => ['hygrometer', 'hygrostat', 'hygrostat_min', 'hygrostat_max'],
                ],
                'Filtry' => [
                    'Filtr ewaporacyjny' => ['evaporative_filter', 'evaporative_filter_life', 'evaporative_filter_price'],
                    'Filtr HEPA' => ['hepa_filter', 'hepa_filter_class', 'effectiveness_hepa_filter', 'hepa_filter_service_life', 'hepa_filter_price'],
                    'Filtr węglowy' => ['carbon_filter', 'carbon_filter_service_life', 'carbon_filter_price'],
                ],
                'Funkcje' => [
                    'Jonizator' => ['ionization', 'ionizer_type', 'ionizer_switch'],
                    'Inne funkcje' => ['uvc', 'mobile_app', 'remote_control', 'heating_and_cooling_function', 'cooling_function'],
                    'Czujniki' => ['pm2_sensor', 'lzo_tvcop_sensor', 'temperature_sensor', 'humidity_sensor', 'light_sensor'],
                ],
                'Wymiary' => [
                    'Wymiary' => ['width', 'height', 'depth', 'weight', 'colors'],
                ],
                'Klasyfikacja' => [
                    'Klasyfikacja' => ['type_of_device', 'main_ranking', 'ranking_hidden', 'for_kids', 'bedroom', 'smokers', 'office', 'kindergarten', 'astmatic', 'alergic'],
                ],
            ],
            'air_humidifiers' => [
                'Podstawowe informacje' => [
                    'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                    'Ranking' => ['capability_points', 'profitability_points', 'popularity'],
                    'Linki partnerskie' => ['partner_link_url', 'partner_link_rel_2'],
                    'Linki Ceneo' => ['ceneo_url', 'ceneo_link_rel_2'],
                    'Galeria' => ['gallery'],
                ],
                'Wydajność' => [
                    'Wydajność' => ['max_humidification_efficiency', 'max_area', 'humidification_type'],
                    'Głośność wentylatora' => ['min_fan_volume', 'max_fan_volume'],
                    'Pobór mocy' => ['min_rated_power_consumption', 'max_rated_power_consumption'],
                ],
                'Zbiornik na wodę' => [
                    'Zbiornik na wodę' => ['water_tank_capacity', 'tank_fill_type'],
                ],
                'Sterowanie' => [
                    'Funkcje smart' => ['mobile_app', 'wifi_24ghz', 'wifi_5ghz', 'display', 'remote_control'],
                ],
                'Filtry' => [
                    'Filtr ewaporacyjny' => ['evaporative_filter', 'evaporative_filter_life', 'evaporative_filter_cost'],
                    'Filtr węglowy' => ['carbon_filter', 'carbon_filter_cost', 'carbon_filter_life'],
                ],
                'Wymiary' => [
                    'Wymiary' => ['width', 'height', 'depth', 'weight', 'colors'],
                ],
            ],
            'air_conditioners' => [
                'Podstawowe informacje' => [
                    'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                    'Oceny i ranking' => ['capability_points', 'profitability_points', 'popularity'],
                    'Linki partnerskie' => ['partner_link_url', 'partner_link_rel_2'],
                    'Linki Ceneo' => ['ceneo_url', 'ceneo_link_rel_2'],
                    'Galeria' => ['gallery'],
                ],
                'Wydajność chłodzenia' => [
                    'Parametry chłodzenia' => ['max_cooling_power', 'min_cooling_power', 'max_area_cooling'],
                ],
                'Wydajność grzania' => [
                    'Parametry grzania' => ['max_heating_power', 'min_heating_power'],
                ],
                'Tryby pracy i funkcje' => [
                    'Tryby pracy' => ['swing_function'],
                ],
                'Specyfikacja techniczna' => [
                    'Chłodziwo' => ['refrigerant_type', 'refrigerant_amount', 'needs_refill'],
                    'Wymiary i waga' => ['width', 'height', 'depth', 'weight'],
                ],
            ],
            'dehumidifiers' => [
                'Podstawowe informacje' => [
                    'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                    'Oceny i ranking' => ['capability_points', 'profitability_points', 'popularity'],
                    'Linki partnerskie' => ['partner_link_url', 'partner_link_rel_2'],
                    'Linki Ceneo' => ['ceneo_url', 'ceneo_link_rel_2'],
                    'Galeria' => ['gallery'],
                ],
                'Wydajność osuszania' => [
                    'Parametry osuszania' => ['max_dehumidification_efficiency', 'max_area'],
                ],
                'Specyfikacja techniczna' => [
                    'Chłodziwo' => ['refrigerant_type', 'refrigerant_amount', 'needs_refill'],
                    'Wymiary i waga' => ['width', 'height', 'depth', 'weight'],
                ],
            ],
            'upright_vacuums' => [
                'Podstawowe informacje' => [
                    'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                    'Oceny i ranking' => ['capability_points', 'profitability_points', 'popularity'],
                    'Linki partnerskie' => ['partner_link_url', 'partner_link_rel_2'],
                    'Linki Ceneo' => ['ceneo_url', 'ceneo_link_rel_2'],
                    'Galeria' => ['gallery'],
                ],
                'Moc i wydajność' => [
                    'Parametry ssania' => ['suction_power', 'motor_type'],
                ],
                'Zasilanie i bateria' => [
                    'Zasilanie' => ['power_supply', 'cable_length'],
                    'Bateria' => ['battery_capacity', 'charging_time'],
                ],
            ],
            'sensors' => [
                'Podstawowe informacje' => [
                    'Podstawowe informacje' => ['status', 'model', 'brand_name', 'price', 'price_before', 'price_date', 'discount_info'],
                    'Linki partnerskie' => ['partner_link_url', 'partner_link_rel_2'],
                    'Ceneo' => ['ceneo_url', 'ceneo_link_rel_2'],
                ],
                'Czujniki PM' => [
                    'Czujnik PM1' => ['pm1', 'pm1_accuracy'],
                    'Czujnik PM2.5' => ['pm25', 'pm25_accuracy'],
                    'Czujnik PM10' => ['pm10', 'pm10_accuracy'],
                ],
            ],
        ];

        return $maps[$tableName] ?? [];
    }
}
