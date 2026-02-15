<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Components\FormFieldSearch;
use App\Filament\Imports\UprightVacuumImporter;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\EditUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\ListUprightVacuums;
use App\Models\UprightVacuum;
use App\Services\CustomFieldService;
use App\Services\ExportActionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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

        return $schema
            ->components([
                FormFieldSearch::make(),
                Tabs::make('Formularz Odkurzacza Pionowego')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Podstawowe informacje')
                            ->schema([
                                Section::make('Podstawowe informacje')
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

                                        TextInput::make('brand_name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Marka'),

                                        Select::make('type')
                                            ->label('Typ odkurzacza')
                                            ->options([
                                                'pionowy' => 'Pionowy',
                                                'reczny' => 'Ręczny',
                                                '2w1' => '2w1 (pionowy + ręczny)',
                                                'myjacy' => 'Myjący',
                                                'workowy' => 'Workowy',
                                                'bezworkowy' => 'Bezworkowy',
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

                                        TextInput::make('image')
                                            ->disabled(),

                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Linki partnerskie')
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

                                Section::make('Linki Ceneo')
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

                                Section::make('Link do recenzji')
                                    ->schema([
                                        Textarea::make('review_link')
                                            ->label('Link do recenzji')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Moc i wydajność')
                            ->schema([
                                Section::make('Parametry ssania')
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

                                        TextInput::make('automatic_power_adjustment')
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

                                Section::make('Silnik')
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

                        Tab::make('Zasilanie i bateria')
                            ->schema([
                                Section::make('Zasilanie')
                                    ->schema([
                                        Select::make('power_supply')
                                            ->multiple()
                                            ->options([
                                                'Akumulatorowe' => 'Akumulatorowe',
                                                'Sieciowe' => 'Sieciowe',
                                            ])
                                            ->label('Typ zasilania')
                                            ->live(),

                                        TextInput::make('cable_length')
                                            ->numeric()
                                            ->suffix('m')
                                            ->label('Długość kabla')
                                            ->visible(fn (callable $get) => in_array('Sieciowe', $get('power_supply') ?? [])),
                                    ])->columns(2),

                                Section::make('Bateria')
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

                        Tab::make('Funkcje czyszczenia')
                            ->schema([
                                Section::make('Funkcje mopowania')
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

                                Section::make('Zbiorniki')
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

                        Tab::make('Filtry i technologie')
                            ->schema([
                                Section::make('System filtracji')
                                    ->schema([
                                        Select::make('pollution_filtration_system')
                                            ->label('System filtracji zanieczyszczeń')
                                            ->options([
                                                'cyklonowy' => 'Cyklonowy',
                                                'wielocyklonowy' => 'Wielocyklonowy',
                                                'workowy' => 'Workowy',
                                                'wodny' => 'Wodny',
                                                'inny' => 'Inny',
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

                                Section::make('Dodatkowe technologie')
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

                        Tab::make('Szczotki i akcesoria')
                            ->schema([
                                Section::make('Szczotki')
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

                                Section::make('Wyposażenie dodatkowe')
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

                                        TagsInput::make('additional_equipment')
                                            ->label('Dodatkowe wyposażenie')
                                            ->columnSpanFull(),

                                        Toggle::make('continuous_work')
                                            ->label('Praca ciągła'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Wyświetlacz i sterowanie')
                            ->schema([
                                Section::make('Wyświetlacz')
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

                                Section::make('Czas pracy')
                                    ->schema([
                                        TextInput::make('vacuuming_time_max')
                                            ->label('Maksymalny czas odkurzania'),

                                        TextInput::make('warranty')
                                            ->numeric()
                                            ->suffix('miesięcy')
                                            ->label('Gwarancja'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Dodatkowe informacje')
                            ->schema([
                                Section::make('Wygląd i wymiary')
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

                                Section::make('Przeznaczenie')
                                    ->schema([
                                        Toggle::make('for_pet_owners')
                                            ->label('Dla właścicieli zwierząt'),

                                        Toggle::make('for_allergy_sufferers')
                                            ->label('Dla alergików'),
                                    ])->columns(2),

                                Section::make('Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Ocena możliwości'),

                                        TextInput::make('profitability')
                                            ->numeric()
                                            ->label('Ocena opłacalności'),

                                        TextInput::make('ranking')
                                            ->numeric()
                                            ->label('Pozycja w rankingu'),

                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),

                                        Toggle::make('main_ranking')
                                            ->label('Główny ranking'),

                                        TextInput::make('videorecenzja1')
                                            ->label('Link do wideo recenzji'),
                                    ])->columns(2),
                            ]),

                        Tab::make('custom_fields')
                            ->schema(
                                $customFieldSchema
                            )
                            ->visible(fn () => count($customFieldSchema) > 0),
                    ]),
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
                        'tableFilters' => [
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
