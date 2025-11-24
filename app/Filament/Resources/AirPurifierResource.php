<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use App\Services\CustomFieldService;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Action;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AirPurifierResource\Pages\ListAirPurifiers;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier;
use App\Enums\Status;
use App\Filament\Imports\AirPurifierImporter;
use App\Filament\Resources\AirPurifierResource\Pages;
use App\Models\AirPurifier;
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
use App\Enums\IonizerType;

final class AirPurifierResource extends Resource
{
    protected static ?string $model = AirPurifier::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Oczyszczacze Powietrza';

    protected static ?string $pluralLabel = 'Oczyszczacze Powietrza';

    protected static ?string $label = 'Oczyszczacze Powietrza';

    protected static string | \UnitEnum | null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_purifiers');

        return $schema
            ->components([
                Tabs::make('Air Purifier Form')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->schema([
                                Select::make('status')
                                    ->default('draft')
                                    ->selectablePlaceholder(false)
                                    ->options(Status::getOptions())
                                    ,

                                TextInput::make('model')
                                    ->maxLength(255),

                                TextInput::make('brand_name')
                                    ->maxLength(255),

                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('zł'),

                                TextInput::make('price_before')
                                    ->numeric()
                                    ->prefix('zł')
                                    ->label('Cena przed'),

                                Textarea::make('discount_info')
                                    ->label('Informacje o zniżce')
                                    ->columnSpanFull(),

                                DateTimePicker::make('price_date')
                                    ->default(now())
                                    ->seconds(false),

                                Section::make('Partner Links')
                                    ->schema([
                                        TextInput::make('partner_link_url')
                                            ->maxLength(255)
                                            ->label('Partner Link URL'),

                                        Select::make('partner_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Partner Link Rel Attributes'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Ceneo Links')
                                    ->schema([
                                        TextInput::make('ceneo_url')
                                            ->maxLength(255)
                                            ->label('Ceneo URL'),

                                        Select::make('ceneo_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Ceneo Link Rel Attributes'),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Review Link')
                                    ->schema([
                                        TextInput::make('review_link')
                                            ->maxLength(255)
                                            ->label('Review Link URL'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Performance')
                            ->columns(4)
                            ->schema([
                                TextInput::make('max_performance')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_area')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_area_ro')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('min_loudness')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_loudness')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('min_rated_power_consumption')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('Min. pobór prądu'),

                                TextInput::make('max_rated_power_consumption')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('Max. pobór prądu'),

                                TextInput::make('capability_points')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('profitability_points')
                                    ->numeric()
                                    ->nullable(),
                            ]),

                        Tab::make('Humidification')
                            ->columns(4)
                            ->schema([
                                Toggle::make('has_humidification'),

                                Select::make('humidification_type')
                                    ->options([
                                        'vapor' => 'Vapor',
                                        'ultrasonic' => 'Ultrasonic',
                                        'evaporative' => 'Evaporative',
                                    ])
                                    ->visible(fn (callable $get) => $get('has_humidification')),

                                Toggle::make('humidification_switch')
                                    ->visible(fn (callable $get) => $get('has_humidification')),

                                TextInput::make('humidification_efficiency')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn (callable $get) => $get('has_humidification')),

                                TextInput::make('humidification_area')
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable()
                                    ->visible(fn (callable $get) => $get('has_humidification')),

                                TextInput::make('water_tank_capacity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn (callable $get) => $get('has_humidification')),

                                Toggle::make('hygrometer'),

                                Toggle::make('hygrostat'),
                            ]),

                        Tab::make('Filters')
                            ->schema([
                                Toggle::make('evaporative_filter')->live(),
                                Section::make('Filtr ewaporacyjny')
                                    ->schema([
                                        TextInput::make('evaporative_filter_life')
                                            ->numeric(),
                                        TextInput::make('evaporative_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn (callable $get) => $get('evaporative_filter')),

                                Toggle::make('hepa_filter')->live(),
                                Section::make('Filtr HEPA')
                                    ->schema([
                                        TextInput::make('hepa_filter_class'),
                                        TextInput::make('effectiveness_hepa_filter')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100),
                                        TextInput::make('hepa_filter_service_life')
                                            ->numeric(),
                                        TextInput::make('hepa_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn (callable $get) => $get('hepa_filter')),

                                Toggle::make('carbon_filter')->live(),
                                Section::make('Filtr węglowy')
                                    ->schema([
                                        TextInput::make('carbon_filter_service_life')
                                            ->numeric(),
                                        TextInput::make('carbon_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn (callable $get) => $get('carbon_filter')),

                                Toggle::make('mesh_filter'),

                                Textarea::make('filter_costs'),
                            ]),

                        Tab::make('Features')
                            ->schema([
                                Toggle::make('ionization')->live(),
                                Section::make('Ionizer')
                                    ->schema([
                                        Select::make('ionizer_type')
                                            ->label('Typ Jonizatora')
                                            ->options(IonizerType::getOptions()),
                                        Toggle::make('ionizer_switch'),
                                    ])
                                    ->visible(fn (callable $get) => $get('ionization')),

                                Section::make('Other Features')
                                    ->schema([
                                        Toggle::make('uvc'),

                                        Toggle::make('mobile_app'),

                                        Toggle::make('remote_control'),

                                        TagsInput::make('functions_and_equipment')
                                            ->placeholder('Add function')
                                            ->separator(','),

                                        Toggle::make('heating_and_cooling_function'),

                                        Toggle::make('cooling_function'),
                                    ])->collapsible(),

                                Section::make('Sensors')
                                    ->schema([
                                        Toggle::make('pm2_sensor'),

                                        Toggle::make('lzo_tvcop_sensor'),

                                        Toggle::make('temperature_sensor'),

                                        Toggle::make('humidity_sensor'),

                                        Toggle::make('light_sensor'),
                                    ])->collapsible(),

                                TagsInput::make('certificates')
                                    ->placeholder('Add certificate')
                                    ->separator(','),
                            ]),

                        Tab::make('Physical Attributes')
                            ->columns(4)
                            ->schema([
                                TextInput::make('width')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('height')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('depth')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('weight')
                                    ->numeric()
                                    ->step(0.1),

                                TagsInput::make('colors')
                                    ->columnSpanFull()
                                    ->placeholder('Dodaj kolor')
                                    ->separator(','),
                            ]),

                        Tab::make('Classification')
                            ->schema([
                                TagsInput::make('type_of_device')
                                    ->placeholder('Add device type')
                                    ->separator(','),

                                Toggle::make('main_ranking'),

                                Toggle::make('ranking_hidden'),

                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('for_kids'),

                                        Toggle::make('bedroom'),

                                        Toggle::make('smokers'),

                                        Toggle::make('office'),

                                        Toggle::make('kindergarten'),

                                        Toggle::make('astmatic'),

                                        Toggle::make('alergic'),
                                    ]),
                            ]),

                        Tab::make('Timestamps')
                            ->schema([
                                DateTimePicker::make('date_created')
                                    ->disabled(),

                                DateTimePicker::make('date_updated')
                                    ->disabled(),

                                DateTimePicker::make('created_at')
                                    ->disabled(),

                                DateTimePicker::make('updated_at')
                                    ->disabled(),
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
        $availableColumns = CustomFieldService::getTableColumns('air_purifiers');

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                ImportAction::make('Import Products')
                    ->importer(AirPurifierImporter::class),
                ExportActionService::createExportAllAction('air_purifiers'),
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn () => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'air_purifiers',
                            ],
                        ],
                    ])),
            ])
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('air_purifiers'),
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
            'index' => ListAirPurifiers::route('/'),
            'create' => CreateAirPurifier::route('/create'),
            'edit' => EditAirPurifier::route('/{record}/edit'),
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
