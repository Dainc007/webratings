<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use App\Services\CustomFieldService;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Actions\ImportAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\UprightVacuumResource\Pages\ListUprightVacuums;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\EditUprightVacuum;
use App\Filament\Imports\UprightVacuumImporter;
use App\Filament\Resources\UprightVacuumResource\Pages;
use App\Models\UprightVacuum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class UprightVacuumResource extends Resource
{
    protected static ?string $model = UprightVacuum::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Odkurzacze Pionowe';

    protected static ?string $pluralLabel = 'Odkurzacze Pionowe';

    protected static ?string $label = 'Odkurzacz Pionowy';

    protected static string | \UnitEnum | null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('upright_vacuums');

        return $schema
            ->components([
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

                                        TextInput::make('type')
                                            ->label('Typ odkurzacza'),

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

                                        Toggle::make('is_promo')
                                            ->label('Promocja'),
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

                                        Textarea::make('review_link')
                                            ->label('Link do recenzji')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),
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
                                            ->label('Typ zasilania'),

                                        TextInput::make('cable_length')
                                            ->numeric()
                                            ->suffix('m')
                                            ->label('Długość kabla'),
                                    ])->columns(2),

                                Section::make('Bateria')
                                    ->schema([
                                        TextInput::make('battery_change')
                                            ->label('Wymiana baterii'),

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

                                        TextInput::make('displaying_battery_status')
                                            ->label('Wyświetlanie stanu baterii'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Funkcje czyszczenia')
                            ->schema([
                                Section::make('Funkcje mopowania')
                                    ->schema([
                                        TextInput::make('mopping_function')
                                            ->label('Funkcja mopowania'),

                                        TextInput::make('active_washing_function')
                                            ->label('Aktywna funkcja mycia'),

                                        TextInput::make('self_cleaning_function')
                                            ->label('Funkcja samoczyszczenia'),

                                        TextInput::make('self_cleaning_underlays')
                                            ->label('Samoczyszczenie podkładek'),

                                        TextInput::make('mopping_time_max')
                                            ->label('Maksymalny czas mopowania'),

                                        TextInput::make('type_of_washing')
                                            ->label('Typ mycia'),
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
                                        TextInput::make('pollution_filtration_system')
                                            ->label('System filtracji zanieczyszczeń'),

                                        TextInput::make('cyclone_technology')
                                            ->label('Technologia cyklonowa'),

                                        TextInput::make('mesh_filter')
                                            ->label('Filtr siatkowy'),

                                        TextInput::make('hepa_filter')
                                            ->label('Filtr HEPA'),

                                        TextInput::make('epa_filter')
                                            ->label('Filtr EPA'),
                                    ])->columns(2),

                                Section::make('Dodatkowe technologie')
                                    ->schema([
                                        TextInput::make('uv_technology')
                                            ->label('Technologia UV'),

                                        TextInput::make('led_backlight')
                                            ->label('Podświetlenie LED'),

                                        TextInput::make('detecting_dirt_on_the_floor')
                                            ->label('Wykrywanie brudu na podłodze'),

                                        TextInput::make('detecting_carpet')
                                            ->label('Wykrywanie dywanu'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Szczotki i akcesoria')
                            ->schema([
                                Section::make('Szczotki')
                                    ->schema([
                                        TextInput::make('electric_brush')
                                            ->label('Elektroszczotka'),

                                        TextInput::make('turbo_brush')
                                            ->label('Turboszczotka'),

                                        TextInput::make('carpet_and_floor_brush')
                                            ->label('Szczotka do dywanów i podłóg'),

                                        TextInput::make('attachment_for_pets')
                                            ->label('Końcówka dla zwierząt'),

                                        TextInput::make('bendable_pipe')
                                            ->label('Giętka rura'),

                                        TextInput::make('telescopic_tube')
                                            ->label('Rura teleskopowa'),
                                    ])->columns(2),

                                Section::make('Wyposażenie dodatkowe')
                                    ->schema([
                                        TextInput::make('hand_vacuum_cleaner')
                                            ->label('Odkurzacz ręczny'),

                                        TextInput::make('charging_station')
                                            ->label('Stacja ładująca'),

                                        TagsInput::make('additional_equipment')
                                            ->label('Dodatkowe wyposażenie')
                                            ->columnSpanFull(),

                                        TextInput::make('continuous_work')
                                            ->label('Praca ciągła'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Wyświetlacz i sterowanie')
                            ->schema([
                                Section::make('Wyświetlacz')
                                    ->schema([
                                        TextInput::make('display')
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
                                            ->label('Waga części ręcznej'),
                                    ])->columns(2),

                                Section::make('Przeznaczenie')
                                    ->schema([
                                        TextInput::make('for_pet_owners')
                                            ->label('Dla właścicieli zwierząt'),

                                        TextInput::make('for_allergy_sufferers')
                                            ->label('Dla alergików'),
                                    ])->columns(2),

                                Section::make('Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability_points')
                                            ->numeric()
                                            ->label('Punkty za możliwości'),

                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Ocena możliwości'),

                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->label('Punkty za opłacalność'),

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
                            ),
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
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn () => route('filament.admin.resources.table-column-preferences.index', [
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

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }
}
