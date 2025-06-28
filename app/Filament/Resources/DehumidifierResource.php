<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DehumidifierResource\Pages;
use App\Filament\Resources\DehumidifierResource\RelationManagers;
use App\Models\Dehumidifier;
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
use App\Filament\Imports\DehumidifierImporter;
use Filament\Tables\Actions\ImportAction;

class DehumidifierResource extends Resource
{
    protected static ?string $model = Dehumidifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye-dropper';
    protected static ?string $navigationLabel = 'Osuszacze Powietrza';
    protected static ?string $pluralLabel = 'Osuszacze Powietrza';
    protected static ?string $label = 'Osuszacz Powietrza';
    protected static ?string $navigationGroup = 'Produkty';
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Formularz Osuszacza')
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
                            
                                            ->label('Typ'),

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

                        Tabs\Tab::make('Wydajność osuszania')
                            ->schema([
                                Section::make('Parametry osuszania')
                                    ->schema([
                                        TextInput::make('max_performance_dry')
                                            ->numeric()
                                            ->suffix('l/24h')
                                            ->label('Maksymalna wydajność osuszania'),

                                        TextInput::make('other_performance_dry')
                                            ->numeric()
                                            ->suffix('l/24h')
                                            ->label('Inna wydajność osuszania'),

                                        TextInput::make('max_performance_dry_condition')
                                            ->label('Warunki maksymalnej wydajności'),

                                        TextInput::make('other_performance_condition')
                                            ->label('Warunki innej wydajności'),

                                        TextInput::make('max_drying_area_manufacturer')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia osuszania (producent)'),

                                        TextInput::make('max_drying_area_ro')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia osuszania (RO)'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Warunki pracy')
                            ->schema([
                                Section::make('Zakres temperatur')
                                    ->schema([
                                        TextInput::make('minimum_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Minimalna temperatura'),

                                        TextInput::make('maximum_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Maksymalna temperatura'),
                                    ])->columns(2),

                                Section::make('Zakres wilgotności')
                                    ->schema([
                                        TextInput::make('minimum_humidity')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Minimalna wilgotność'),

                                        TextInput::make('maximum_humidity')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Maksymalna wilgotność'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Zbiornik na wodę')
                            ->schema([
                                Section::make('Parametry zbiornika')
                                    ->schema([
                                        TextInput::make('water_tank_capacity')
                                            ->numeric()
                                            ->suffix('l')
                                            ->label('Pojemność zbiornika na wodę'),

                                        TextInput::make('minimum_fill_time')
                                            ->numeric()
                                            ->suffix('h')
                                            ->label('Minimalny czas napełniania'),

                                        TextInput::make('average_filling_time')
                                            ->numeric()
                                            ->suffix('h')
                                            ->label('Średni czas napełniania'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Higrostat i sterowanie')
                            ->schema([
                                Section::make('Higrostat')
                                    ->schema([
                                        TagsInput::make('higrostat')
                                            ->label('Higrostat')
                                            ->columnSpanFull(),

                                        TextInput::make('min_value_for_hygrostat')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Minimalna wartość higrostatu'),

                                        TextInput::make('max_value_for_hygrostat')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Maksymalna wartość higrostatu'),

                                        TextInput::make('increment_of_the_hygrostat')
                                            ->label('Krok higrostatu'),
                                    ])->columns(2),

                                Section::make('Wentylator')
                                    ->schema([
                                        TextInput::make('number_of_fan_speeds')
                                            ->numeric()
                                            ->label('Liczba prędkości wentylatora'),

                                        TextInput::make('max_air_flow')
                                            ->numeric()
                                            ->suffix('m³/h')
                                            ->label('Maksymalny przepływ powietrza'),
                                    ])->columns(2),

                                Section::make('Hałas')
                                    ->schema([
                                        TextInput::make('max_loudness')
                                            ->numeric()
                                            ->suffix('dB')
                                            ->label('Maksymalny poziom hałasu'),

                                        TextInput::make('min_loudness')
                                            ->numeric()
                                            ->suffix('dB')
                                            ->label('Minimalny poziom hałasu'),
                                    ])->columns(2),

                                Section::make('Tryby pracy')
                                    ->schema([
                                        TagsInput::make('modes_of_operation')
                                            ->label('Tryby pracy')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Filtry i oczyszczanie')
                            ->schema([
                                Section::make('Filtry podstawowe')
                                    ->schema([
                                        Toggle::make('mesh_filter')
                                            ->label('Filtr siatkowy'),
                                    ])->columns(1),

                                Section::make('Filtr HEPA')
                                    ->schema([
                                        Toggle::make('hepa_filter')
                                            ->live()
                                            ->label('Filtr HEPA'),

                                        TextInput::make('hepa_filter_price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->visible(fn(callable $get) => $get('hepa_filter'))
                                            ->label('Cena filtra HEPA'),

                                        TextInput::make('hepa_service_life')
                                            ->numeric()
                                            ->suffix('miesięcy')
                                            ->visible(fn(callable $get) => $get('hepa_filter'))
                                            ->label('Żywotność filtra HEPA'),
                                    ])->columns(2)->collapsible(),

                                Section::make('Filtr węglowy')
                                    ->schema([
                                        Toggle::make('carbon_filter')
                                            ->live()
                                            ->label('Filtr węglowy'),

                                        TextInput::make('carbon_filter_price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->visible(fn(callable $get) => $get('carbon_filter'))
                                            ->label('Cena filtra węglowego'),

                                        TextInput::make('carbon_service_life')
                                            ->numeric()
                                            ->suffix('miesięcy')
                                            ->visible(fn(callable $get) => $get('carbon_filter'))
                                            ->label('Żywotność filtra węglowego'),
                                    ])->columns(2)->collapsible(),

                                Section::make('Dodatkowe technologie')
                                    ->schema([
                                        Toggle::make('ionization')
                                            ->label('Jonizacja'),

                                        Toggle::make('uvc')
                                            ->live()
                                            ->label('Lampa UV-C'),

                                        Toggle::make('uv_light_generator')
                                            ->visible(fn(callable $get) => $get('uvc'))
                                            ->label('Generator światła UV'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Sterowanie i łączność')
                            ->schema([
                                Section::make('Sterowanie')
                                    ->schema([
                                        Toggle::make('mobile_app')
                                            ->live()
                                            ->label('Aplikacja mobilna'),

                                        TagsInput::make('mobile_features')
                                            ->visible(fn(callable $get) => $get('mobile_app'))
                                            ->label('Funkcje aplikacji mobilnej')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Funkcje i wyposażenie')
                                    ->schema([
                                        TagsInput::make('functions')
                                            ->label('Funkcje')
                                            ->columnSpanFull(),

                                        TagsInput::make('functions_and_equipment_dehumi')
                                            ->label('Funkcje i wyposażenie osuszacza')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Specyfikacja techniczna')
                            ->schema([
                                Section::make('Chłodziwo')
                                    ->schema([
                                        TextInput::make('refrigerant_kind')
                                            ->label('Rodzaj chłodziwa'),

                                        TextInput::make('refrigerant_amount')
                                            ->numeric()
                                            ->suffix('kg')
                                            ->label('Ilość chłodziwa'),

                                        TextInput::make('needs_to_be_completed')
                                            ->label('Wymaga uzupełnienia'),
                                    ])->columns(2),

                                Section::make('Parametry elektryczne')
                                    ->schema([
                                        TextInput::make('rated_power_consumption')
                                            ->numeric()
                                            ->suffix('W')
                                            ->label('Zużycie energii'),

                                        TextInput::make('rated_voltage')
                                            ->numeric()
                                            ->suffix('V')
                                            ->label('Napięcie znamionowe'),
                                    ])->columns(2),

                                Section::make('Wymiary i waga')
                                    ->schema([
                                        TextInput::make('width')
                                            ->numeric()
                                            ->suffix('cm')
                                            ->label('Szerokość'),

                                        TextInput::make('height')
                                            ->numeric()
                                            ->suffix('cm')
                                            ->label('Wysokość'),

                                        TextInput::make('depth')
                                            ->numeric()
                                            ->suffix('cm')
                                            ->label('Głębokość'),

                                        TextInput::make('weight')
                                            ->numeric()
                                            ->suffix('kg')
                                            ->label('Waga'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Dodatkowe informacje')
                            ->schema([
                                Section::make('Galeria i dokumentacja')
                                    ->schema([
                                        TagsInput::make('gallery')
                                            ->label('Galeria zdjęć')
                                            ->columnSpanFull(),

                                        TextInput::make('manual_file')
                                            ->disabled()
                                            ->label('Plik instrukcji'),
                                    ]),

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
                                    ])->columns(2),

                                Section::make('Dane systemowe')
                                    ->schema([
                                        TextInput::make('remote_id')
                                            ->numeric()
                                            ->label('ID zewnętrzne'),

                                        TextInput::make('sort')
                                            ->numeric()
                                            ->label('Kolejność sortowania'),

                                        TextInput::make('user_created')
                                            ->label('Utworzony przez'),

                                        DateTimePicker::make('date_created')
                                            ->label('Data utworzenia'),

                                        TextInput::make('user_updated')
                                            ->label('Zaktualizowany przez'),

                                        DateTimePicker::make('date_updated')
                                            ->label('Data aktualizacji'),
                                    ])->columns(2)->collapsible(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(false)
        ->headerActions([
            Tables\Actions\ImportAction::make('Import Dehumidifiers')
                ->importer(DehumidifierImporter::class),
            Tables\Actions\Action::make('Ustawienia')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                    'tableFilters' => [
                        'table_name' => [
                            'value' => 'dehumidifiers',
                        ],
                    ],
                ])),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('remote_id')
                    ->numeric()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_before')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('partner_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ceneo_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_performance_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('other_performance_condition')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_performance_dry_condition')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_drying_area_manufacturer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('other_performance_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_drying_area_ro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('depth')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_humidity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_humidity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_power_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('refrigerant_kind')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refrigerant_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('needs_to_be_completed')
                    ->searchable(),
                Tables\Columns\TextColumn::make('water_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_fill_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_filling_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_value_for_hygrostat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_value_for_hygrostat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('increment_of_the_hygrostat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_fan_speeds')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_air_flow')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_loudness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_loudness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('mesh_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hepa_service_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hepa_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('hepa_filter')
                    ->boolean(),
                Tables\Columns\IconColumn::make('carbon_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('carbon_service_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('carbon_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ionization')
                    ->boolean(),
                Tables\Columns\IconColumn::make('uvc')
                    ->boolean(),
                Tables\Columns\IconColumn::make('uv_light_generator')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mobile_app')
                    ->boolean(),
                Tables\Columns\TextColumn::make('manual_file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capability_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ranking')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ranking_hidden')
                    ->boolean(),
                Tables\Columns\IconColumn::make('main_ranking')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_promo')
                    ->boolean(),
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
            'index' => Pages\ListDehumidifiers::route('/'),
            'create' => Pages\CreateDehumidifier::route('/create'),
            'edit' => Pages\EditDehumidifier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
