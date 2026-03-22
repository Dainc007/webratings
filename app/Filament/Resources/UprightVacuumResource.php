<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Components\FormFieldSearch;
use App\Filament\Imports\UprightVacuumImporter;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\EditUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\ListUprightVacuums;
use App\Models\UprightVacuum;
use App\Models\Brand;
use App\Services\CustomFieldService;
use App\Services\FormLayoutService;
use App\Services\LabelService;
use App\Services\ExportActionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class UprightVacuumResource extends Resource
{
    protected static ?string $model = UprightVacuum::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Odkurzacze Pionowe';

    protected static ?string $pluralLabel = 'Odkurzacze Pionowe';

    protected static ?string $label = 'Odkurzacz Pionowy';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('upright_vacuums');

        $defaultTabs = [
            Tab::make(LabelService::tab('upright_vacuums', 'Podstawowe informacje'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Podstawowe informacje')
                                    ->schema([
                                        Select::make('status')
                                            ->selectablePlaceholder(false)
                                            ->options([
                                                'draft' => 'Szkic',
                                                'published' => 'Opublikowany',
                                                'archived' => 'Zarchiwizowany',
                                            ])
                                            ->required()
                                            ->label('Status'),

                                        TextInput::make('model')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Model'),

                                        Select::make('brand_name')
                                            ->label('Marka')
                                            ->required()
                                            ->searchable()
                                            ->getSearchResultsUsing(fn (string $search): array =>
                                                Brand::where('name', 'like', '%' . mb_strtolower($search) . '%')
                                                    ->limit(50)
                                                    ->get()
                                                    ->pluck('name', 'name')
                                                    ->toArray()
                                            )
                                            ->getOptionLabelUsing(fn (?string $value): ?string => $value)
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nazwa marki')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(fn (array $data): string =>
                                                Brand::firstOrCreate(['name' => $data['name']])->name
                                            ),

                                        Select::make('type')
                                            ->label('Typ odkurzacza')
                                            ->options([
                                                'pionowy' => 'Pionowy',
                                                'pionowy_mycie' => 'Pionowy z funkcją mycia',
                                                'pionowy_led' => 'Pionowy z funkcją podświetlenia LED',
                                                'pionowy_elektroszczotka' => 'Pionowy z elektroszczotką',
                                                'odkurzacz_myjacy' => 'Odkurzacz myjący',
                                                'mop_elektryczny' => 'Mop elektryczny',
                                            ])
                                            ->searchable(),

                                        TextInput::make('price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->label('Cena'),

                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->label('Cena przed'),

                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Linki partnerskie')
                                    ->schema([
                                        TextInput::make('partner_name')
                                            ->label('Nazwa partnera'),

                                        Textarea::make('partner_link_url')
                                            ->label('Link partnerski')
                                            ->columnSpanFull(),

                                        Select::make('partner_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Atrybuty rel partnera'),

                                        TextInput::make('partner_link_title')
                                            ->label('Tytuł linku partnera'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                LabelService::sectionMake('upright_vacuums', 'Linki Ceneo')
                                    ->schema([
                                        Textarea::make('ceneo_url')
                                            ->label('Link Ceneo')
                                            ->columnSpanFull(),

                                        Select::make('ceneo_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Atrybuty rel Ceneo'),

                                        TextInput::make('ceneo_link_title')
                                            ->label('Tytuł linku Ceneo'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                LabelService::sectionMake('upright_vacuums', 'Link do recenzji')
                                    ->schema([
                                        Textarea::make('review_link')
                                            ->label('Link do recenzji')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                LabelService::sectionMake('upright_vacuums', 'Galeria')
                                    ->schema([
                                        FileUpload::make('local_gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('upright-vacuums')
                                            ->image()
                                            ->multiple()
                                            ->preserveFilenames()
                                            ->imagePreviewHeight('250')
                                            ->panelLayout('grid')
                                            ->reorderable()
                                            ->appendFiles()
                                            ->openable()
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                LabelService::sectionMake('upright_vacuums', 'Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Ocena możliwości'),

                                        TextInput::make('profitability')
                                            ->numeric()
                                            ->label('Ocena opłacalności'),

                                        TextInput::make('capability_points')
                                            ->numeric()
                                            ->label('Punkty możliwości'),

                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->label('Punkty opłacalności'),

                                        TextInput::make('ranking')
                                            ->numeric()
                                            ->label('Pozycja w rankingu'),

                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),

                                        Toggle::make('main_ranking')
                                            ->label('Główny ranking'),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Moc i wydajność'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Parametry ssania')
                                    ->schema([
                                        Select::make('vacuum_cleaner_type')
                                            ->multiple()
                                            ->options([
                                                'bezworkowy' => 'Bezworkowy',
                                                'workowy' => 'Workowy',
                                            ])
                                            ->label('Typ odkurzacza'),

                                        TextInput::make('suction_power_aw')
                                            ->numeric()
                                            ->suffix('AW')
                                            ->label('Moc ssania (AW)'),

                                        TextInput::make('suction_power_pa')
                                            ->numeric()
                                            ->suffix('Pa')
                                            ->label('Moc ssania (Pa)'),

                                        TextInput::make('number_of_suction_power_levels')
                                            ->numeric()
                                            ->label('Liczba poziomów mocy ssania'),

                                        Toggle::make('automatic_power_adjustment')
                                            ->label('Automatyczna regulacja mocy'),

                                        TextInput::make('suction_power_highest_level_pa')
                                            ->numeric()
                                            ->suffix('Pa')
                                            ->label('Moc ssania - najwyższy poziom'),

                                        TextInput::make('suction_power_medium_level_pa')
                                            ->numeric()
                                            ->suffix('Pa')
                                            ->label('Moc ssania - średni poziom'),

                                        TextInput::make('suction_power_low_level_pa')
                                            ->numeric()
                                            ->suffix('Pa')
                                            ->label('Moc ssania - niski poziom'),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Silnik')
                                    ->schema([
                                        TextInput::make('maximum_engine_power')
                                            ->numeric()
                                            ->suffix('W')
                                            ->label('Maksymalna moc silnika'),

                                        TextInput::make('rotation_speed')
                                            ->numeric()
                                            ->suffix('obr/min')
                                            ->label('Prędkość obrotowa'),

                                        TextInput::make('noise_level')
                                            ->numeric()
                                            ->suffix('dB')
                                            ->label('Poziom hałasu'),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Zasilanie i bateria'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Zasilanie')
                                    ->schema([
                                        CheckboxList::make('power_supply')
                                            ->options([
                                                'Akumulatorowe' => 'Akumulatorowe',
                                                'Sieciowe' => 'Sieciowe',
                                            ])
                                            ->label('Typ zasilania')
                                            ->columns(2)
                                            ->live(),

                                        TextInput::make('cable_length')
                                            ->numeric()
                                            ->suffix('m')
                                            ->label('Długość kabla')
                                            ->disabled(fn (callable $get) => ! in_array('Sieciowe', $get('power_supply') ?? []))
                                            ->dehydrated(),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Bateria')
                                    ->schema([
                                        Select::make('battery_change')
                                            ->label('Wymiana baterii')
                                            ->options([
                                                'tak' => 'Tak - możliwa wymiana',
                                                'nie' => 'Nie - bateria wbudowana',
                                                'ograniczona' => 'Ograniczona - wymaga serwisu',
                                            ]),

                                        TextInput::make('maximum_operation_time')
                                            ->label('Maksymalny czas pracy'),

                                        TextInput::make('battery_charging_time')
                                            ->label('Czas ładowania baterii'),

                                        TextInput::make('battery_voltage')
                                            ->numeric()
                                            ->suffix('V')
                                            ->label('Napięcie baterii'),

                                        TextInput::make('battery_capacity')
                                            ->numeric()
                                            ->suffix('mAh')
                                            ->label('Pojemność baterii'),

                                        TextInput::make('operation_time_turbo')
                                            ->label('Czas pracy - tryb turbo'),

                                        TextInput::make('operation_time_eco')
                                            ->label('Czas pracy - tryb eco'),

                                        Select::make('displaying_battery_status')
                                            ->label('Wyświetlanie stanu baterii')
                                            ->options([
                                                'diody_led' => 'Diody LED',
                                                'wyswietlacz_lcd' => 'Wyświetlacz LCD',
                                                'wyswietlacz_led' => 'Wyświetlacz LED',
                                                'procent' => 'Procent na wyświetlaczu',
                                                'brak' => 'Brak wskaźnika',
                                            ]),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Funkcje czyszczenia'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Funkcje mopowania')
                                    ->schema([
                                        Toggle::make('mopping_function')
                                            ->label('Funkcja mopowania'),

                                        Toggle::make('active_washing_function')
                                            ->label('Aktywna funkcja mycia'),

                                        Toggle::make('self_cleaning_function')
                                            ->label('Funkcja samoczyszczenia'),

                                        Toggle::make('self_cleaning_underlays')
                                            ->label('Samoczyszczenie podkładek'),

                                        TextInput::make('mopping_time_max')
                                            ->numeric()
                                            ->suffix('min')
                                            ->label('Maksymalny czas mopowania'),

                                        Select::make('type_of_washing')
                                            ->label('Typ mycia')
                                            ->multiple()
                                            ->options([
                                                'suche' => 'Suche',
                                                'mokre' => 'Mokre',
                                                'parowe' => 'Parowe',
                                                'hybrydowe' => 'Hybrydowe (suche + mokre)',
                                            ]),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Zbiorniki')
                                    ->schema([
                                        TextInput::make('clean_water_tank_capacity')
                                            ->numeric()
                                            ->suffix('l')
                                            ->label('Pojemność zbiornika czystej wody'),

                                        TextInput::make('dirty_water_tank_capacity')
                                            ->numeric()
                                            ->suffix('l')
                                            ->label('Pojemność zbiornika brudnej wody'),

                                        TextInput::make('dust_tank_capacity')
                                            ->numeric()
                                            ->suffix('l')
                                            ->label('Pojemność zbiornika na kurz'),

                                        TextInput::make('easy_emptying_tank')
                                            ->label('Łatwe opróżnianie zbiornika'),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Filtry i technologie'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'System filtracji')
                                    ->schema([
                                        Select::make('pollution_filtration_system')
                                            ->label('System filtracji zanieczyszczeń')
                                            ->options([
                                                '1-stopniowy' => '1-stopniowy',
                                                '2-stopniowy' => '2-stopniowy',
                                                '3-stopniowy' => '3-stopniowy',
                                                '4-stopniowy' => '4-stopniowy',
                                                '5-stopniowy' => '5-stopniowy',
                                            ]),

                                        Toggle::make('cyclone_technology')
                                            ->label('Technologia cyklonowa'),

                                        Toggle::make('mesh_filter')
                                            ->label('Filtr wstępny'),

                                        Toggle::make('hepa_filter')
                                            ->label('Filtr HEPA'),

                                        Toggle::make('epa_filter')
                                            ->label('Filtr EPA'),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Dodatkowe technologie')
                                    ->schema([
                                        Toggle::make('uv_technology')
                                            ->label('Technologia UV'),

                                        Toggle::make('led_backlight')
                                            ->label('Podświetlenie LED'),

                                        Toggle::make('detecting_dirt_on_the_floor')
                                            ->label('Wykrywanie brudu na podłodze'),

                                        Toggle::make('detecting_carpet')
                                            ->label('Wykrywanie dywanu'),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Szczotki i akcesoria'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Szczotki')
                                    ->schema([
                                        Toggle::make('electric_brush')
                                            ->label('Elektroszczotka'),

                                        Toggle::make('turbo_brush')
                                            ->label('Turboszczotka'),

                                        Toggle::make('carpet_and_floor_brush')
                                            ->label('Szczotka do dywanów i podłóg'),

                                        Toggle::make('attachment_for_pets')
                                            ->label('Końcówka dla zwierząt'),

                                        Toggle::make('bendable_pipe')
                                            ->label('Giętka rura'),

                                        Toggle::make('telescopic_tube')
                                            ->label('Rura teleskopowa'),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Wyposażenie dodatkowe')
                                    ->schema([
                                        Toggle::make('hand_vacuum_cleaner')
                                            ->label('Odkurzacz ręczny'),

                                        Select::make('charging_station')
                                            ->label('Stacja ładująca')
                                            ->multiple()
                                            ->options([
                                                'brak' => 'Brak',
                                                'scienna' => 'Ścienna',
                                                'stojaca' => 'Stojąca',
                                                'stacja_dokujaca' => 'Stacja dokująca',
                                                'podstawka' => 'Podstawka ładująca',
                                            ]),

                                        Select::make('additional_equipment')
                                            ->label('Dodatkowe wyposażenie')
                                            ->multiple()
                                            ->searchable()
                                            ->options([
                                                'Ssawka szczelinowa' => 'Ssawka szczelinowa',
                                                'Końcówka 2w1 do kurzu' => 'Końcówka 2w1 do kurzu',
                                                'Mini elektroszczotka' => 'Mini elektroszczotka',
                                                'Miękka szczotka' => 'Miękka szczotka',
                                                'Elastyczny adapter' => 'Elastyczny adapter',
                                            ])
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nazwa wyposażenia')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(fn (array $data): string => $data['name'])
                                            ->columnSpanFull(),

                                        Toggle::make('continuous_work')
                                            ->label('Praca ciągła'),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Wyświetlacz i sterowanie'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Wyświetlacz')
                                    ->schema([
                                        Toggle::make('display')
                                            ->label('Wyświetlacz'),

                                        Select::make('display_type')
                                            ->multiple()
                                            ->options([
                                                'LCD' => 'LCD',
                                                'LED' => 'LED',
                                                'TFT' => 'TFT',
                                                'OLED' => 'OLED',
                                                'cyfrowy' => 'Cyfrowy',
                                            ])
                                            ->label('Typ wyświetlacza'),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Czas pracy')
                                    ->schema([
                                        TextInput::make('vacuuming_time_max')
                                            ->label('Maksymalny czas odkurzania'),

                                        TextInput::make('warranty')
                                            ->numeric()
                                            ->suffix('miesięcy')
                                            ->label('Gwarancja'),
                                    ])->columns(2),
                            ]),

                        Tab::make(LabelService::tab('upright_vacuums', 'Dodatkowe informacje'))
                            ->schema([
                                LabelService::sectionMake('upright_vacuums', 'Wygląd i wymiary')
                                    ->schema([
                                        TagsInput::make('colors')
                                            ->label('Dostępne kolory')
                                            ->columnSpanFull(),

                                        TextInput::make('weight')
                                            ->numeric()
                                            ->suffix('kg')
                                            ->label('Waga'),

                                        TextInput::make('weight_hand')
                                            ->numeric()
                                            ->suffix('kg')
                                            ->label('Waga w ręce'),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Przeznaczenie')
                                    ->schema([
                                        Select::make('for_pet_owners')
                                            ->label('Dla właścicieli zwierząt')
                                            ->options([
                                                'tak' => 'Tak',
                                                'nie' => 'Nie',
                                            ]),

                                        Select::make('for_allergy_sufferers')
                                            ->label('Dla alergików')
                                            ->options([
                                                'tak' => 'Tak',
                                                'nie' => 'Nie',
                                            ]),
                                    ])->columns(2),

                                LabelService::sectionMake('upright_vacuums', 'Wideo')
                                    ->schema([
                                        TextInput::make('videorecenzja1')
                                            ->label('Link do wideo recenzji'),
                                    ])->columns(2),
                            ]),

            Tab::make('custom_fields')
                ->schema(
                    $customFieldSchema
                )
                ->visible(fn () => count($customFieldSchema) > 0),
        ];

        return $schema
            ->components([
                FormFieldSearch::make(),
                Tabs::make('Formularz Odkurzacza Pionowego')
                    ->columnSpanFull()
                    ->tabs(FormLayoutService::applyLayout('upright_vacuums', $defaultTabs)),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = CustomFieldService::getTableColumns('upright_vacuums');

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->headerActions([
                ImportAction::make('Import Upright Vacuums')
                    ->importer(UprightVacuumImporter::class),
                ExportActionService::createExportAllAction('upright_vacuums'),
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
                        'filters' => [
                            'table_name' => [
                                'value' => 'upright_vacuums',
                            ],
                        ],
                    ])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('upright_vacuums'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUprightVacuums::route('/'),
            'create' => CreateUprightVacuum::route('/create'),
            'edit' => EditUprightVacuum::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::count();
    }
}
