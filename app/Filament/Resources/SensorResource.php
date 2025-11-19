<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use App\Services\CustomFieldService;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ImportAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SensorResource\Pages\ListSensors;
use App\Filament\Resources\SensorResource\Pages\CreateSensor;
use App\Filament\Resources\SensorResource\Pages\EditSensor;
use App\Filament\Imports\SensorImporter;
use App\Filament\Resources\SensorResource\Pages;
use App\Models\Sensor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\ExportActionService;

final class SensorResource extends Resource
{
    protected static ?string $model = Sensor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Sensors';

    protected static ?string $pluralLabel = 'Sensors';

    protected static ?string $label = 'Sensors';

    protected static string | \UnitEnum | null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('sensors');

        return $schema
            ->components([
                Tabs::make('Sensor form')
                    ->tabs([
                        Tab::make('Basic information')
                            ->schema([
                                Section::make('Basic information')
                                    ->schema([
                                        Select::make('status')
                                            ->selectablePlaceholder(false)
                                            ->options([
                                                'draft' => __('sensors.fields.status.options.draft'),
                                                'published' => __('sensors.fields.status.options.published'),
                                                'archived' => __('sensors.fields.status.options.archived'),
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
                                            ->disabled(),

                                        Textarea::make('discount_info')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Partner Links')
                                    ->schema([
                                        TextInput::make('partner_name')
                                            ->label('Partner Name'),

                                        Textarea::make('partner_link_url')
                                            ->label('Partner Link URL')
                                            ->columnSpanFull(),

                                        Select::make('partner_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Partner Link Rel Attributes'),

                                        TextInput::make('partner_link_title')
                                            ->label('Partner Link Title'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Ceneo Links')
                                    ->schema([
                                        Textarea::make('ceneo_url')
                                            ->label('Ceneo URL')
                                            ->columnSpanFull(),

                                        Select::make('ceneo_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Ceneo Link Rel Attributes'),

                                        TextInput::make('ceneo_link_title')
                                            ->label('Ceneo Link Title'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Review Link')
                                    ->schema([
                                        Textarea::make('review_link')
                                            ->label('Review Link URL')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('PM sensors')
                            ->schema([
                                Section::make(__('sensors.sections.pm1_sensor'))
                                    ->schema([
                                        Toggle::make('is_pm1')
                                            ->live(),

                                        TextInput::make('pm1_range')
                                            ->visible(fn (Get $get): mixed => $get('is_pm1')),

                                        TextInput::make('pm1_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_pm1')),

                                        TextInput::make('pm1_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_pm1')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.pm2_sensor'))
                                    ->schema([
                                        Toggle::make('is_pm2')
                                            ->live(),

                                        TextInput::make('pm2_range')
                                            ->visible(fn (Get $get): mixed => $get('is_pm2')),

                                        TextInput::make('pm2_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_pm2')),

                                        TextInput::make('pm2_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_pm2')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.pm10_sensor'))
                                    ->schema([
                                        Toggle::make('is_pm10')
                                            ->live(),

                                        TextInput::make('pm10_range')
                                            ->visible(fn (Get $get): mixed => $get('is_pm10')),

                                        TextInput::make('pm10_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_pm10')),

                                        TextInput::make('pm10_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_pm10')),
                                    ])->columns(2),
                            ]),

                        Tab::make('Chemical sensors')
                            ->schema([
                                Section::make(__('sensors.sections.lzo_sensor'))
                                    ->schema([
                                        Toggle::make('is_lzo')
                                            ->live(),

                                        TextInput::make('lzo_range')
                                            ->visible(fn (Get $get): mixed => $get('is_lzo')),

                                        TextInput::make('lzo_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_lzo')),

                                        TextInput::make('lzo_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_lzo')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.hcho_sensor'))
                                    ->schema([
                                        Toggle::make('is_hcho')
                                            ->live(),

                                        TextInput::make('hcho_range')
                                            ->visible(fn (Get $get): mixed => $get('is_hcho')),

                                        TextInput::make('hcho_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_hcho')),

                                        TextInput::make('hcho_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_hcho')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.co2_sensor'))
                                    ->schema([
                                        Toggle::make('is_co2')
                                            ->live(),

                                        TextInput::make('co2_range')
                                            ->visible(fn (Get $get): mixed => $get('is_co2')),

                                        TextInput::make('co2_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_co2')),

                                        TextInput::make('co2_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_co2')),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.co_sensor'))
                                    ->schema([
                                        Toggle::make('is_co')
                                            ->live(),

                                        TextInput::make('co_range')
                                            ->visible(fn (Get $get): mixed => $get('is_co')),

                                        TextInput::make('co_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_co')),

                                        TextInput::make('co_sensor_type')
                                            ->visible(fn (Get $get): mixed => $get('is_co')),
                                    ])->columns(2),
                            ]),

                        Tab::make('Environmental sensors')
                            ->schema([
                                Section::make(__('sensors.sections.temperature_sensor'))
                                    ->schema([
                                        Toggle::make('is_temperature')
                                            ->live(),

                                        TextInput::make('temperature_range')
                                            ->visible(fn (Get $get): mixed => $get('is_temperature')),

                                        TextInput::make('temperature_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_temperature')),

                                        TextInput::make('temperature'),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.humidity_sensor'))
                                    ->schema([
                                        Toggle::make('is_humidity')
                                            ->live(),

                                        TextInput::make('humidity_range')
                                            ->visible(fn (Get $get): mixed => $get('is_humidity')),

                                        TextInput::make('humidity_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_humidity')),

                                        TextInput::make('humidity'),
                                    ])->columns(2),

                                Section::make(__('sensors.sections.pressure_sensor'))
                                    ->schema([
                                        Toggle::make('is_pressure')
                                            ->live(),

                                        TextInput::make('pressure_range')
                                            ->visible(fn (Get $get): mixed => $get('is_pressure')),

                                        TextInput::make('pressure_accuracy')
                                            ->visible(fn (Get $get): mixed => $get('is_pressure')),
                                    ])->columns(2),
                            ]),

                        Tab::make('Power connectivity')
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

                        Tab::make('Device features')
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

                        Tab::make('Dimensions performance')
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

                        Tab::make('Ranking')
                            ->schema([
                                Section::make(__('sensors.sections.ranking_settings'))
                                    ->schema([
                                        TextInput::make('ranking')
                                            ->numeric(),

                                        Toggle::make('ranking_hidden'),

                                        Toggle::make('main_ranking'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Metadata')
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

                        Tab::make('custom_fields')
                            ->schema(
                                $customFieldSchema
                            ),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = CustomFieldService::getTableColumns('sensors');

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->filters([])
            ->headerActions([
                ImportAction::make('Import Sensors')
                    ->importer(SensorImporter::class),
                ExportActionService::createExportAllAction('sensors'),
                Action::make('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn () => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'sensors',
                            ],
                        ],
                    ])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('sensors'),
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
            'index' => ListSensors::route('/'),
            'create' => CreateSensor::route('/create'),
            'edit' => EditSensor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
