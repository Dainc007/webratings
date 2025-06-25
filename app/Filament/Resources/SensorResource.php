<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorResource\Pages;
use App\Filament\Resources\SensorResource\RelationManagers;
use App\Models\Sensor;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
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
use App\Filament\Imports\SensorImporter;

class SensorResource extends Resource
{
    protected static ?string $model = Sensor::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Czujniki';

    protected static ?string $pluralLabel = 'Czujniki';

    protected static ?string $label = 'Czujnik';

    protected static ?string $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Formularz Czujnika')
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

                                        TextInput::make('price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->label('Cena'),

                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->label('Cena przed'),

                                        Forms\Components\FileUpload::make('image')
                                            ->image()
                                            ->label('Zdjęcie'),

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

                        Tabs\Tab::make('Czujniki PM')
                            ->schema([
                                Section::make('Czujnik PM1')
                                    ->schema([
                                        Toggle::make('is_pm1')
                                            ->live()
                                            ->label('Posiada czujnik PM1'),

                                        TextInput::make('pm1_range')
                                            ->visible(fn(callable $get) => $get('is_pm1'))
                                            ->label('Zakres PM1'),

                                        TextInput::make('pm1_accuracy')
                                            ->visible(fn(callable $get) => $get('is_pm1'))
                                            ->label('Dokładność PM1'),

                                        TextInput::make('pm1_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_pm1'))
                                            ->label('Typ czujnika PM1'),
                                    ])->columns(2),

                                Section::make('Czujnik PM2.5')
                                    ->schema([
                                        Toggle::make('is_pm2')
                                            ->live()
                                            ->label('Posiada czujnik PM2.5'),

                                        TextInput::make('pm2_range')
                                            ->visible(fn(callable $get) => $get('is_pm2'))
                                            ->label('Zakres PM2.5'),

                                        TextInput::make('pm2_accuracy')
                                            ->visible(fn(callable $get) => $get('is_pm2'))
                                            ->label('Dokładność PM2.5'),

                                        TextInput::make('pm2_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_pm2'))
                                            ->label('Typ czujnika PM2.5'),
                                    ])->columns(2),

                                Section::make('Czujnik PM10')
                                    ->schema([
                                        Toggle::make('is_pm10')
                                            ->live()
                                            ->label('Posiada czujnik PM10'),

                                        TextInput::make('pm10_range')
                                            ->visible(fn(callable $get) => $get('is_pm10'))
                                            ->label('Zakres PM10'),

                                        TextInput::make('pm10_accuracy')
                                            ->visible(fn(callable $get) => $get('is_pm10'))
                                            ->label('Dokładność PM10'),

                                        TextInput::make('pm10_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_pm10'))
                                            ->label('Typ czujnika PM10'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Czujniki chemiczne')
                            ->schema([
                                Section::make('Czujnik LZO (Ozon)')
                                    ->schema([
                                        Toggle::make('is_lzo')
                                            ->live()
                                            ->label('Posiada czujnik LZO'),

                                        TextInput::make('lzo_range')
                                            ->visible(fn(callable $get) => $get('is_lzo'))
                                            ->label('Zakres LZO'),

                                        TextInput::make('lzo_accuracy')
                                            ->visible(fn(callable $get) => $get('is_lzo'))
                                            ->label('Dokładność LZO'),

                                        TextInput::make('lzo_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_lzo'))
                                            ->label('Typ czujnika LZO'),
                                    ])->columns(2),

                                Section::make('Czujnik HCHO (Formaldehyd)')
                                    ->schema([
                                        Toggle::make('is_hcho')
                                            ->live()
                                            ->label('Posiada czujnik HCHO'),

                                        TextInput::make('hcho_range')
                                            ->visible(fn(callable $get) => $get('is_hcho'))
                                            ->label('Zakres HCHO'),

                                        TextInput::make('hcho_accuracy')
                                            ->visible(fn(callable $get) => $get('is_hcho'))
                                            ->label('Dokładność HCHO'),

                                        TextInput::make('hcho_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_hcho'))
                                            ->label('Typ czujnika HCHO'),
                                    ])->columns(2),

                                Section::make('Czujnik CO2')
                                    ->schema([
                                        Toggle::make('is_co2')
                                            ->live()
                                            ->label('Posiada czujnik CO2'),

                                        TextInput::make('co2_range')
                                            ->visible(fn(callable $get) => $get('is_co2'))
                                            ->label('Zakres CO2'),

                                        TextInput::make('co2_accuracy')
                                            ->visible(fn(callable $get) => $get('is_co2'))
                                            ->label('Dokładność CO2'),

                                        TextInput::make('co2_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_co2'))
                                            ->label('Typ czujnika CO2'),
                                    ])->columns(2),

                                Section::make('Czujnik CO')
                                    ->schema([
                                        Toggle::make('is_co')
                                            ->live()
                                            ->label('Posiada czujnik CO'),

                                        TextInput::make('co_range')
                                            ->visible(fn(callable $get) => $get('is_co'))
                                            ->label('Zakres CO'),

                                        TextInput::make('co_accuracy')
                                            ->visible(fn(callable $get) => $get('is_co'))
                                            ->label('Dokładność CO'),

                                        TextInput::make('co_sensor_type')
                                            ->visible(fn(callable $get) => $get('is_co'))
                                            ->label('Typ czujnika CO'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Czujniki środowiskowe')
                            ->schema([
                                Section::make('Czujnik temperatury')
                                    ->schema([
                                        Toggle::make('is_temperature')
                                            ->live()
                                            ->label('Posiada czujnik temperatury'),

                                        TextInput::make('temperature_range')
                                            ->visible(fn(callable $get) => $get('is_temperature'))
                                            ->label('Zakres temperatury'),

                                        TextInput::make('temperature_accuracy')
                                            ->visible(fn(callable $get) => $get('is_temperature'))
                                            ->label('Dokładność temperatury'),

                                        TextInput::make('temperature')
                                            ->label('Aktualna temperatura'),
                                    ])->columns(2),

                                Section::make('Czujnik wilgotności')
                                    ->schema([
                                        Toggle::make('is_humidity')
                                            ->live()
                                            ->label('Posiada czujnik wilgotności'),

                                        TextInput::make('humidity_range')
                                            ->visible(fn(callable $get) => $get('is_humidity'))
                                            ->label('Zakres wilgotności'),

                                        TextInput::make('humidity_accuracy')
                                            ->visible(fn(callable $get) => $get('is_humidity'))
                                            ->label('Dokładność wilgotności'),

                                        TextInput::make('humidity')
                                            ->label('Aktualna wilgotność'),
                                    ])->columns(2),

                                Section::make('Czujnik ciśnienia')
                                    ->schema([
                                        Toggle::make('is_pressure')
                                            ->live()
                                            ->label('Posiada czujnik ciśnienia'),

                                        TextInput::make('pressure_range')
                                            ->visible(fn(callable $get) => $get('is_pressure'))
                                            ->label('Zakres ciśnienia'),

                                        TextInput::make('pressure_accuracy')
                                            ->visible(fn(callable $get) => $get('is_pressure'))
                                            ->label('Dokładność ciśnienia'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Zasilanie i łączność')
                            ->schema([
                                Section::make('Zasilanie')
                                    ->schema([
                                        TextInput::make('battery')
                                            ->label('Typ baterii'),

                                        TextInput::make('battery_capacity')
                                            ->numeric()
                                            ->label('Pojemność baterii (mAh)'),

                                        TextInput::make('voltage')
                                            ->numeric()
                                            ->label('Napięcie (V)'),

                                        Toggle::make('has_power_cord')
                                            ->label('Posiada przewód zasilający'),
                                    ])->columns(2),

                                Section::make('Łączność')
                                    ->schema([
                                        Toggle::make('wifi')
                                            ->label('Wi-Fi'),

                                        Toggle::make('bluetooth')
                                            ->label('Bluetooth'),

                                        TagsInput::make('mobile_features')
                                            ->separator(',')
                                            ->label('Funkcje mobilne')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Funkcje urządzenia')
                            ->schema([
                                Section::make('Funkcje')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('has_history')
                                                    ->label('Historia pomiarów'),

                                                Toggle::make('has_display')
                                                    ->label('Wyświetlacz'),

                                                Toggle::make('has_alarm')
                                                    ->label('Alarm'),

                                                Toggle::make('has_assessment')
                                                    ->label('Ocena jakości'),

                                                Toggle::make('has_outdoor_indicator')
                                                    ->label('Wskaźnik zewnętrzny'),

                                                Toggle::make('has_battery_indicator')
                                                    ->label('Wskaźnik baterii'),

                                                Toggle::make('has_clock')
                                                    ->label('Zegar'),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Wymiary i wydajność')
                            ->schema([
                                Section::make('Wymiary fizyczne')
                                    ->schema([
                                        TextInput::make('width')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm')
                                            ->label('Szerokość'),

                                        TextInput::make('height')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm')
                                            ->label('Wysokość'),

                                        TextInput::make('depth')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm')
                                            ->label('Głębokość'),

                                        TextInput::make('weight')
                                            ->numeric()
                                            ->step(0.01)
                                            ->suffix('kg')
                                            ->label('Waga'),
                                    ])->columns(2),

                                Section::make('Ocena wydajności')
                                    ->schema([
                                        TextInput::make('capability_points')
                                            ->numeric()
                                            ->label('Punkty możliwości'),

                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Możliwości'),

                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->step(0.01)
                                            ->label('Punkty opłacalności'),

                                        TextInput::make('profitability')
                                            ->numeric()
                                            ->label('Opłacalność'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Ranking')
                            ->schema([
                                Section::make('Ustawienia rankingu')
                                    ->schema([
                                        TextInput::make('ranking')
                                            ->numeric()
                                            ->label('Pozycja w rankingu'),

                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),

                                        TextInput::make('main_ranking')
                                            ->label('Główny ranking'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Metadane')
                            ->schema([
                                Section::make('Identyfikatory systemu')
                                    ->schema([
                                        TextInput::make('remote_id')
                                            ->numeric()
                                            ->label('ID zdalne'),

                                        TextInput::make('sort')
                                            ->numeric()
                                            ->label('Kolejność sortowania'),

                                        TextInput::make('user_created')
                                            ->disabled()
                                            ->label('Utworzone przez'),

                                        TextInput::make('user_updated')
                                            ->disabled()
                                            ->label('Zaktualizowane przez'),
                                    ])->columns(2),

                                Section::make('Znaczniki czasu')
                                    ->schema([
                                        DateTimePicker::make('date_created')
                                            ->disabled()
                                            ->label('Data utworzenia'),

                                        DateTimePicker::make('date_updated')
                                            ->disabled()
                                            ->label('Data aktualizacji'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->headerActions([
            Tables\Actions\ImportAction::make('Import Sensors')
                ->importer(SensorImporter::class),
            Tables\Actions\Action::make('Ustawienia')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                    'tableFilters' => [
                        'table_name' => [
                            'value' => 'sensors',
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
                Tables\Columns\IconColumn::make('is_pm1')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pm1_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm1_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm1_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_pm2')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pm2_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm2_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm2_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_pm10')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pm10_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm10_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm10_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_lzo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('lzo_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lzo_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lzo_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_hcho')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hcho_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hcho_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hcho_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_co2')
                    ->boolean(),
                Tables\Columns\TextColumn::make('co2_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co2_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co2_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_co')
                    ->boolean(),
                Tables\Columns\TextColumn::make('co_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_temperature')
                    ->boolean(),
                Tables\Columns\TextColumn::make('temperature_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('temperature_accuracy')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_humidity')
                    ->boolean(),
                Tables\Columns\TextColumn::make('humidity_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('humidity_accuracy')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_pressure')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pressure_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pressure_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('battery')
                    ->searchable(),
                Tables\Columns\TextColumn::make('battery_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_power_cord')
                    ->boolean(),
                Tables\Columns\IconColumn::make('wifi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bluetooth')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_history')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_display')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_alarm')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_assessment')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_outdoor_indicator')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_battery_indicator')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_clock')
                    ->boolean(),
                Tables\Columns\TextColumn::make('temperature')
                    ->searchable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('depth')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('main_ranking')
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
            'index' => Pages\ListSensors::route('/'),
            'create' => Pages\CreateSensor::route('/create'),
            'edit' => Pages\EditSensor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
