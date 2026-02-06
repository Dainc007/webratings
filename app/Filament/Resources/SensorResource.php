<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Imports\SensorImporter;
use App\Filament\Resources\SensorResource\Pages\CreateSensor;
use App\Filament\Resources\SensorResource\Pages\EditSensor;
use App\Filament\Resources\SensorResource\Pages\ListSensors;
use App\Models\Sensor;
use App\Services\CustomFieldService;
use App\Services\ExportActionService;
use App\Services\FormLayoutService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class SensorResource extends Resource
{
    protected static ?string $model = Sensor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Sensors';

    protected static ?string $pluralLabel = 'Sensors';

    protected static ?string $label = 'Sensors';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'model';

    public static function getFieldDefinitions(): array
    {
        return [
            'status' => fn () => Select::make('status')
                ->selectablePlaceholder(false)
                ->options([
                    'draft' => __('sensors.fields.status.options.draft'),
                    'published' => __('sensors.fields.status.options.published'),
                    'archived' => __('sensors.fields.status.options.archived'),
                ])
                ->required(),

            'model' => fn () => TextInput::make('model')
                ->required()
                ->maxLength(255),

            'brand_name' => fn () => TextInput::make('brand_name')
                ->required()
                ->maxLength(255),

            'price' => fn () => TextInput::make('price')
                ->numeric()
                ->prefix('PLN'),

            'price_before' => fn () => TextInput::make('price_before')
                ->numeric()
                ->prefix('PLN'),

            'image' => fn () => TextInput::make('image')
                ->disabled(),

            'discount_info' => fn () => Textarea::make('discount_info')
                ->columnSpanFull(),

            'partner_name' => fn () => TextInput::make('partner_name')
                ->label('Partner Name'),

            'partner_link_url' => fn () => Textarea::make('partner_link_url')
                ->label('Partner Link URL')
                ->columnSpanFull(),

            'partner_link_rel_2' => fn () => Select::make('partner_link_rel_2')
                ->multiple()
                ->options([
                    'nofollow' => 'nofollow',
                    'dofollow' => 'dofollow',
                    'sponsored' => 'sponsored',
                    'noopener' => 'noopener',
                ])
                ->label('Partner Link Rel Attributes'),

            'partner_link_title' => fn () => TextInput::make('partner_link_title')
                ->label('Partner Link Title'),

            'ceneo_url' => fn () => Textarea::make('ceneo_url')
                ->label('Ceneo URL')
                ->columnSpanFull(),

            'ceneo_link_rel_2' => fn () => Select::make('ceneo_link_rel_2')
                ->multiple()
                ->options([
                    'nofollow' => 'nofollow',
                    'dofollow' => 'dofollow',
                    'sponsored' => 'sponsored',
                    'noopener' => 'noopener',
                ])
                ->label('Ceneo Link Rel Attributes'),

            'ceneo_link_title' => fn () => TextInput::make('ceneo_link_title')
                ->label('Ceneo Link Title'),

            'review_link' => fn () => Textarea::make('review_link')
                ->label('Review Link URL')
                ->columnSpanFull(),

            'is_pm1' => fn () => Toggle::make('is_pm1')
                ->live(),

            'pm1_range' => fn () => TextInput::make('pm1_range')
                ->visible(fn (callable $get) => $get('is_pm1')),

            'pm1_accuracy' => fn () => TextInput::make('pm1_accuracy')
                ->visible(fn (callable $get) => $get('is_pm1')),

            'pm1_sensor_type' => fn () => TextInput::make('pm1_sensor_type')
                ->visible(fn (callable $get) => $get('is_pm1')),

            'is_pm2' => fn () => Toggle::make('is_pm2')
                ->live(),

            'pm2_range' => fn () => TextInput::make('pm2_range')
                ->visible(fn (callable $get) => $get('is_pm2')),

            'pm2_accuracy' => fn () => TextInput::make('pm2_accuracy')
                ->visible(fn (callable $get) => $get('is_pm2')),

            'pm2_sensor_type' => fn () => TextInput::make('pm2_sensor_type')
                ->visible(fn (callable $get) => $get('is_pm2')),

            'is_pm10' => fn () => Toggle::make('is_pm10')
                ->live(),

            'pm10_range' => fn () => TextInput::make('pm10_range')
                ->visible(fn (callable $get) => $get('is_pm10')),

            'pm10_accuracy' => fn () => TextInput::make('pm10_accuracy')
                ->visible(fn (callable $get) => $get('is_pm10')),

            'pm10_sensor_type' => fn () => TextInput::make('pm10_sensor_type')
                ->visible(fn (callable $get) => $get('is_pm10')),

            'is_lzo' => fn () => Toggle::make('is_lzo')
                ->live(),

            'lzo_range' => fn () => TextInput::make('lzo_range')
                ->visible(fn (callable $get) => $get('is_lzo')),

            'lzo_accuracy' => fn () => TextInput::make('lzo_accuracy')
                ->visible(fn (callable $get) => $get('is_lzo')),

            'lzo_sensor_type' => fn () => TextInput::make('lzo_sensor_type')
                ->visible(fn (callable $get) => $get('is_lzo')),

            'is_hcho' => fn () => Toggle::make('is_hcho')
                ->live(),

            'hcho_range' => fn () => TextInput::make('hcho_range')
                ->visible(fn (callable $get) => $get('is_hcho')),

            'hcho_accuracy' => fn () => TextInput::make('hcho_accuracy')
                ->visible(fn (callable $get) => $get('is_hcho')),

            'hcho_sensor_type' => fn () => TextInput::make('hcho_sensor_type')
                ->visible(fn (callable $get) => $get('is_hcho')),

            'is_co2' => fn () => Toggle::make('is_co2')
                ->live(),

            'co2_range' => fn () => TextInput::make('co2_range')
                ->visible(fn (callable $get) => $get('is_co2')),

            'co2_accuracy' => fn () => TextInput::make('co2_accuracy')
                ->visible(fn (callable $get) => $get('is_co2')),

            'co2_sensor_type' => fn () => TextInput::make('co2_sensor_type')
                ->visible(fn (callable $get) => $get('is_co2')),

            'is_co' => fn () => Toggle::make('is_co')
                ->live(),

            'co_range' => fn () => TextInput::make('co_range')
                ->visible(fn (callable $get) => $get('is_co')),

            'co_accuracy' => fn () => TextInput::make('co_accuracy')
                ->visible(fn (callable $get) => $get('is_co')),

            'co_sensor_type' => fn () => TextInput::make('co_sensor_type')
                ->visible(fn (callable $get) => $get('is_co')),

            'is_temperature' => fn () => Toggle::make('is_temperature')
                ->live(),

            'temperature_range' => fn () => TextInput::make('temperature_range')
                ->visible(fn (callable $get) => $get('is_temperature')),

            'temperature_accuracy' => fn () => TextInput::make('temperature_accuracy')
                ->visible(fn (callable $get) => $get('is_temperature')),

            'temperature' => fn () => TextInput::make('temperature'),

            'is_humidity' => fn () => Toggle::make('is_humidity')
                ->live(),

            'humidity_range' => fn () => TextInput::make('humidity_range')
                ->visible(fn (callable $get) => $get('is_humidity')),

            'humidity_accuracy' => fn () => TextInput::make('humidity_accuracy')
                ->visible(fn (callable $get) => $get('is_humidity')),

            'humidity' => fn () => TextInput::make('humidity'),

            'is_pressure' => fn () => Toggle::make('is_pressure')
                ->live(),

            'pressure_range' => fn () => TextInput::make('pressure_range')
                ->visible(fn (callable $get) => $get('is_pressure')),

            'pressure_accuracy' => fn () => TextInput::make('pressure_accuracy')
                ->visible(fn (callable $get) => $get('is_pressure')),

            'battery' => fn () => TextInput::make('battery'),

            'battery_capacity' => fn () => TextInput::make('battery_capacity')
                ->numeric(),

            'voltage' => fn () => TextInput::make('voltage')
                ->numeric(),

            'has_power_cord' => fn () => Toggle::make('has_power_cord'),

            'wifi' => fn () => Toggle::make('wifi'),

            'bluetooth' => fn () => Toggle::make('bluetooth'),

            'mobile_features' => fn () => TagsInput::make('mobile_features')
                ->separator(',')
                ->columnSpanFull(),

            'has_history' => fn () => Toggle::make('has_history'),

            'has_display' => fn () => Toggle::make('has_display'),

            'has_alarm' => fn () => Toggle::make('has_alarm'),

            'has_assessment' => fn () => Toggle::make('has_assessment'),

            'has_outdoor_indicator' => fn () => Toggle::make('has_outdoor_indicator'),

            'has_battery_indicator' => fn () => Toggle::make('has_battery_indicator'),

            'has_clock' => fn () => Toggle::make('has_clock'),

            'width' => fn () => TextInput::make('width')
                ->numeric()
                ->step(0.1)
                ->suffix('cm'),

            'height' => fn () => TextInput::make('height')
                ->numeric()
                ->step(0.1)
                ->suffix('cm'),

            'depth' => fn () => TextInput::make('depth')
                ->numeric()
                ->step(0.1)
                ->suffix('cm'),

            'weight' => fn () => TextInput::make('weight')
                ->numeric()
                ->step(0.01)
                ->suffix('kg'),

            'capability_points' => fn () => TextInput::make('capability_points')
                ->numeric(),

            'capability' => fn () => TextInput::make('capability')
                ->numeric(),

            'profitability_points' => fn () => TextInput::make('profitability_points')
                ->numeric()
                ->step(0.01),

            'profitability' => fn () => TextInput::make('profitability')
                ->numeric(),

            'ranking' => fn () => TextInput::make('ranking')
                ->numeric(),

            'ranking_hidden' => fn () => Toggle::make('ranking_hidden'),

            'main_ranking' => fn () => Toggle::make('main_ranking'),

            'remote_id' => fn () => TextInput::make('remote_id')
                ->numeric(),

            'sort' => fn () => TextInput::make('sort')
                ->numeric(),

            'user_created' => fn () => TextInput::make('user_created')
                ->disabled(),

            'user_updated' => fn () => TextInput::make('user_updated')
                ->disabled(),

            'date_created' => fn () => DateTimePicker::make('date_created')
                ->disabled(),

            'date_updated' => fn () => DateTimePicker::make('date_updated')
                ->disabled(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('sensors');

        return $schema
            ->components([
                Tabs::make('Sensor form')
                    ->tabs(FormLayoutService::buildForm('sensors', static::getFieldDefinitions(), $customFieldSchema))
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
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
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

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
