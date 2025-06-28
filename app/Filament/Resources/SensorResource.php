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
use Filament\Forms\Get;

class SensorResource extends Resource
{
    protected static ?string $model = Sensor::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Sensors';

    protected static ?string $pluralLabel = 'Sensors';

    protected static ?string $label = 'Sensors';

    protected static ?string $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Sensor form')
                    ->tabs([
                        Tabs\Tab::make('Basic information')
                            ->schema([
                                Section::make('Basic information')
                                    ->schema([
                                        Select::make('status')
                                            ->selectablePlaceholder(false)
                                            ->options([
                                                'draft' => __('sensors.fields.status.options.draft'),
                                                'published' => __('sensors.fields.status.options.published'),
                                                'archived' => __('sensors.fields.status.options.archived')
                                            ])
                                            ->required(),

                                        TextInput::make('model')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('brand_name')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('price')
                                            ->numeric()
                                            ->prefix('PLN'),

                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->prefix('PLN'),

                                        TextInput::make('image')
                                            ->disabled()
                                        ,

                                        Textarea::make('discount_info')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Partner links')
                                    ->schema([
                                        TextInput::make('partner_name'),

                                        Textarea::make('partner_link_url')
                                            ->columnSpanFull(),

                                        Select::make('partner_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ]),

                                        TextInput::make('partner_link_title'),

                                        Textarea::make('ceneo_url')
                                            ->columnSpanFull(),

                                        Select::make('ceneo_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ]),

                                        TextInput::make('ceneo_link_title'),

                                        Textarea::make('review_link')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),
                            ]),

                        Tabs\Tab::make('PM sensors')
                            ->schema([
                                Section::make(__('sensors.sections.pm1_sensor'))
                                    ->schema([
                                        Toggle::make('is_pm1')
                                            ->live(),

                                        TextInput::make('pm1_range')
                                            ->visible(fn(Get $get) => $get('is_pm1')),

                                        TextInput::make('pm1_accuracy')
                                            ->visible(fn(Get $get) => $get('is_pm1')),

                                        TextInput::make('pm1_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_pm1')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.pm2_sensor'))
                                    ->schema([
                                        Toggle::make('is_pm2')
                                            ->live(),

                                        TextInput::make('pm2_range')
                                            ->visible(fn(Get $get) => $get('is_pm2')),

                                        TextInput::make('pm2_accuracy')
                                            ->visible(fn(Get $get) => $get('is_pm2')),

                                        TextInput::make('pm2_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_pm2')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.pm10_sensor'))
                                    ->schema([
                                        Toggle::make('is_pm10')
                                            ->live(),

                                        TextInput::make('pm10_range')
                                            ->visible(fn(Get $get) => $get('is_pm10')),

                                        TextInput::make('pm10_accuracy')
                                            ->visible(fn(Get $get) => $get('is_pm10')),

                                        TextInput::make('pm10_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_pm10')),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Chemical sensors')
                            ->schema([
                                Section::make(__('sensors.sections.lzo_sensor'))
                                    ->schema([
                                        Toggle::make('is_lzo')
                                            ->live(),

                                        TextInput::make('lzo_range')
                                            ->visible(fn(Get $get) => $get('is_lzo')),

                                        TextInput::make('lzo_accuracy')
                                            ->visible(fn(Get $get) => $get('is_lzo')),

                                        TextInput::make('lzo_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_lzo')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.hcho_sensor'))
                                    ->schema([
                                        Toggle::make('is_hcho')
                                            ->live(),

                                        TextInput::make('hcho_range')
                                            ->visible(fn(Get $get) => $get('is_hcho')),

                                        TextInput::make('hcho_accuracy')
                                            ->visible(fn(Get $get) => $get('is_hcho')),

                                        TextInput::make('hcho_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_hcho')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.co2_sensor'))
                                    ->schema([
                                        Toggle::make('is_co2')
                                            ->live(),

                                        TextInput::make('co2_range')
                                            ->visible(fn(Get $get) => $get('is_co2')),

                                        TextInput::make('co2_accuracy')
                                            ->visible(fn(Get $get) => $get('is_co2')),

                                        TextInput::make('co2_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_co2')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.co_sensor'))
                                    ->schema([
                                        Toggle::make('is_co')
                                            ->live(),

                                        TextInput::make('co_range')
                                            ->visible(fn(Get $get) => $get('is_co')),

                                        TextInput::make('co_accuracy')
                                            ->visible(fn(Get $get) => $get('is_co')),

                                        TextInput::make('co_sensor_type')
                                            ->visible(fn(Get $get) => $get('is_co')),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Environmental sensors')
                            ->schema([
                                Section::make(__('sensors.sections.temperature_sensor'))
                                    ->schema([
                                        Toggle::make('is_temperature')
                                            ->live(),

                                        TextInput::make('temperature_range')
                                            ->visible(fn(Get $get) => $get('is_temperature')),

                                        TextInput::make('temperature_accuracy')
                                            ->visible(fn(Get $get) => $get('is_temperature')),

                                        TextInput::make('temperature'),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.humidity_sensor'))
                                    ->schema([
                                        Toggle::make('is_humidity')
                                            ->live(),

                                        TextInput::make('humidity_range')
                                            ->visible(fn(Get $get) => $get('is_humidity')),

                                        TextInput::make('humidity_accuracy')
                                            ->visible(fn(Get $get) => $get('is_humidity')),

                                        TextInput::make('humidity'),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.pressure_sensor'))
                                    ->schema([
                                        Toggle::make('is_pressure')
                                            ->live(),

                                        TextInput::make('pressure_range')
                                            ->visible(fn(Get $get) => $get('is_pressure')),

                                        TextInput::make('pressure_accuracy')
                                            ->visible(fn(Get $get) => $get('is_pressure')),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Power connectivity')
                            ->schema([
                                Section::make(__('sensors.sections.power'))
                                    ->schema([
                                        TextInput::make('battery'),

                                        TextInput::make('battery_capacity')
                                            ->numeric(),

                                        TextInput::make('voltage')
                                            ->numeric(),

                                        Toggle::make('has_power_cord'),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.connectivity'))
                                    ->schema([
                                        Toggle::make('wifi'),

                                        Toggle::make('bluetooth'),

                                        TagsInput::make('mobile_features')
                                            ->separator(',')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Device features')
                            ->schema([
                                Section::make(__('sensors.sections.features'))
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('has_history'),

                                                Toggle::make('has_display'),

                                                Toggle::make('has_alarm'),

                                                Toggle::make('has_assessment'),

                                                Toggle::make('has_outdoor_indicator'),

                                                Toggle::make('has_battery_indicator'),

                                                Toggle::make('has_clock'),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Dimensions performance')
                            ->schema([
                                Section::make(__('sensors.sections.physical_dimensions'))
                                    ->schema([
                                        TextInput::make('width')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm'),

                                        TextInput::make('height')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm'),

                                        TextInput::make('depth')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm'),

                                        TextInput::make('weight')
                                            ->numeric()
                                            ->step(0.01)
                                            ->suffix('kg'),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.performance_rating'))
                                    ->schema([
                                        TextInput::make('capability_points')
                                            ->numeric(),

                                        TextInput::make('capability')
                                            ->numeric(),

                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->step(0.01),

                                        TextInput::make('profitability')
                                            ->numeric(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Ranking')
                            ->schema([
                                Section::make(__('sensors.sections.ranking_settings'))
                                    ->schema([
                                        TextInput::make('ranking')
                                            ->numeric(),

                                        Toggle::make('ranking_hidden'),

                                        Toggle::make('main_ranking'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Metadata')
                            ->schema([
                                Section::make(__('sensors.sections.system_identifiers'))
                                    ->schema([
                                        TextInput::make('remote_id')
                                            ->numeric(),

                                        TextInput::make('sort')
                                            ->numeric(),

                                        TextInput::make('user_created')
                                            ->disabled(),

                                        TextInput::make('user_updated')
                                            ->disabled(),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.timestamps'))
                                    ->schema([
                                        DateTimePicker::make('date_created')
                                            ->disabled(),

                                        DateTimePicker::make('date_updated')
                                            ->disabled(),
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
        ->recordUrl(null)
        ->headerActions([
            Tables\Actions\ImportAction::make('Import Sensors')
                ->importer(SensorImporter::class),
            Tables\Actions\Action::make('Settings')
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
