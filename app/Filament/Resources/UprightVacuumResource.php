<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UprightVacuumResource\Pages;
use App\Filament\Resources\UprightVacuumResource\RelationManagers;
use App\Models\UprightVacuum;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\UprightVacuumImporter;

class UprightVacuumResource extends Resource
{
    protected static ?string $model = UprightVacuum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Odkurzacze Pionowe';
    protected static ?string $pluralLabel = 'Odkurzacze Pionowe';
    protected static ?string $label = 'Odkurzacz Pionowy';
    protected static ?string $navigationGroup = 'Produkty';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Formularz Odkurzacza Pionowego')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Podstawowe informacje')
                            ->schema([
                                Section::make('Podstawowe informacje')
                                    ->schema([
                                        Select::make('status')
                                            ->selectablePlaceholder(false)
                                            ->options([
                                                'draft' => 'Szkic',
                                                'published' => 'Opublikowany',
                                                'archived' => 'Zarchiwizowany'
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

                        Tabs\Tab::make('Moc i wydajność')
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

                        Tabs\Tab::make('Zasilanie i bateria')
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

                        Tabs\Tab::make('Funkcje czyszczenia')
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

                        Tabs\Tab::make('Filtry i technologie')
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

                        Tabs\Tab::make('Szczotki i akcesoria')
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

                        Tabs\Tab::make('Wyświetlacz i sterowanie')
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

                        Tabs\Tab::make('Dodatkowe informacje')
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(false)
        ->headerActions([
            ImportAction::make('Import Upright Vacuums')
                ->importer(UprightVacuumImporter::class),
            Tables\Actions\Action::make('Ustawienia')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                    'tableFilters' => [
                        'table_name' => [
                            'value' => 'upright_vacuums',
                        ],
                    ],
                ])),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('remote_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_created')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_created')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_updated')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_updated')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_before')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ceneo_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suction_power_aw')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suction_power_pa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_suction_power_levels')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('automatic_power_adjustment')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suction_power_highest_level_pa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suction_power_medium_level_pa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suction_power_low_level_pa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_engine_power')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rotation_speed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('noise_level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('battery_change')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cable_length')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_operation_time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('battery_charging_time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('battery_voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('battery_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mopping_function')
                    ->searchable(),
                Tables\Columns\TextColumn::make('active_washing_function')
                    ->searchable(),
                Tables\Columns\TextColumn::make('self_cleaning_function')
                    ->searchable(),
                Tables\Columns\TextColumn::make('self_cleaning_underlays')
                    ->searchable(),
                Tables\Columns\TextColumn::make('clean_water_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dirty_water_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dust_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hand_vacuum_cleaner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('led_backlight')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uv_technology')
                    ->searchable(),
                Tables\Columns\TextColumn::make('detecting_dirt_on_the_floor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('detecting_carpet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('display')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pollution_filtration_system')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cyclone_technology')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mesh_filter')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hepa_filter')
                    ->searchable(),
                Tables\Columns\TextColumn::make('epa_filter')
                    ->searchable(),
                Tables\Columns\TextColumn::make('electric_brush')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bendable_pipe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('turbo_brush')
                    ->searchable(),
                Tables\Columns\TextColumn::make('carpet_and_floor_brush')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attachment_for_pets')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telescopic_tube')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charging_station')
                    ->searchable(),
                Tables\Columns\TextColumn::make('for_pet_owners')
                    ->searchable(),
                Tables\Columns\TextColumn::make('for_allergy_sufferers')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('warranty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ranking')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mopping_time_max')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vacuuming_time_max')
                    ->searchable(),
                Tables\Columns\TextColumn::make('easy_emptying_tank')
                    ->searchable(),
                Tables\Columns\TextColumn::make('continuous_work')
                    ->searchable(),
                Tables\Columns\TextColumn::make('displaying_battery_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operation_time_turbo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operation_time_eco')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight_hand')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_of_washing')
                    ->searchable(),
                Tables\Columns\TextColumn::make('main_ranking')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('ranking_hidden')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_promo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('videorecenzja1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUprightVacuums::route('/'),
            'create' => Pages\CreateUprightVacuum::route('/create'),
            'edit' => Pages\EditUprightVacuum::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
