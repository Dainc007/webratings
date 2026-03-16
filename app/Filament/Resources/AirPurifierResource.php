<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\IonizerType;
use App\Enums\Status;
use App\Filament\Components\FormFieldSearch;
use App\Filament\Imports\AirPurifierImporter;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\ListAirPurifiers;
use App\Models\AirPurifier;
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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use UnitEnum;

final class AirPurifierResource extends Resource
{
    protected static ?string $model = AirPurifier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Oczyszczacze Powietrza';

    protected static ?string $pluralLabel = 'Oczyszczacze Powietrza';

    protected static ?string $label = 'Oczyszczacze Powietrza';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_purifiers');

        $defaultTabs = [
                        Tab::make(LabelService::tab('air_purifiers', 'Podstawowe informacje'))
                            ->schema([
                                LabelService::sectionMake('air_purifiers', 'Podstawowe informacje')
                                    ->schema([
                                        Select::make('status')
                                            ->default('draft')
                                            ->selectablePlaceholder(false)
                                            ->options(Status::getOptions()),

                                        TextInput::make('model')
                                            ->maxLength(255),

                                        Select::make('brand_name')
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

                                        TextInput::make('price')
                                            ->numeric()
                                            ->prefix('zł'),

                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->prefix('zł')
                                            ->label('Cena przed'),

                                        DateTimePicker::make('price_date')
                                            ->default(now())
                                            ->seconds(false),

                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                LabelService::sectionMake('air_purifiers', 'Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability_points')
                                            ->numeric()
                                            ->nullable()
                                            ->label('Punkty za możliwości'),

                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->nullable()
                                            ->label('Punkty za opłacalność'),

                                        TextInput::make('popularity')
                                            ->numeric()
                                            ->nullable()
                                            ->label('Popularność'),
                                    ])
                                    ->columns(2),

                                LabelService::sectionMake('air_purifiers', 'Linki partnerskie')
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

                                LabelService::sectionMake('air_purifiers', 'Ceneo')
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

                                TextInput::make('review_link')
                                    ->maxLength(255)
                                    ->label('Link do recenzji'),

                                LabelService::sectionMake('air_purifiers', 'Galeria')
                                    ->schema([
                                        FileUpload::make('gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('air-purifiers')
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
                            ]),

                        Tab::make(LabelService::tab('air_purifiers', 'Wydajność'))
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

                                TextInput::make('number_of_fan_speeds')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('Liczba prędkości oczyszczania'),

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
                            ]),

                        Tab::make(LabelService::tab('air_purifiers', 'Nawilżanie'))
                            ->schema([
                                LabelService::sectionMake('air_purifiers', 'Nawilżanie')
                                    ->schema([
                                        Toggle::make('has_humidification')
                                            ->label('Posiada nawilżanie')
                                            ->live(),

                                        Select::make('humidification_type')
                                            ->label('Typ nawilżania')
                                            ->options([
                                                'vapor' => 'Parowe',
                                                'ultrasonic' => 'Ultradźwiękowe',
                                                'evaporative' => 'Ewaporacyjne',
                                            ])
                                            ->visible(fn (callable $get) => $get('has_humidification')),

                                        Toggle::make('humidification_switch')
                                            ->label('Możliwość wyłączenia')
                                            ->visible(fn (callable $get) => $get('has_humidification')),

                                        TextInput::make('humidification_area')
                                            ->numeric()
                                            ->minValue(0)
                                            ->nullable()
                                            ->label('Powierzchnia nawilżania')
                                            ->suffix('m²')
                                            ->visible(fn (callable $get) => $get('has_humidification')),

                                        TextInput::make('water_tank_capacity')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label('Pojemność zbiornika na wodę')
                                            ->suffix('l')
                                            ->visible(fn (callable $get) => $get('has_humidification')),

                                        TextInput::make('humidification_efficiency')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label('Wydajność nawilżania')
                                            ->suffix('ml/h')
                                            ->visible(fn (callable $get) => $get('has_humidification')),
                                    ])
                                    ->columns(2),

                                LabelService::sectionMake('air_purifiers', 'Higrostat')
                                    ->schema([
                                        Toggle::make('hygrometer')
                                            ->label('Higrometr'),

                                        Toggle::make('hygrostat')
                                            ->label('Higrostat')
                                            ->live(),

                                        TextInput::make('hygrostat_min')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label('Higrostat min')
                                            ->suffix('%')
                                            ->visible(fn (callable $get) => $get('hygrostat')),

                                        TextInput::make('hygrostat_max')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label('Higrostat max')
                                            ->suffix('%')
                                            ->visible(fn (callable $get) => $get('hygrostat')),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(LabelService::tab('air_purifiers', 'Filtry'))
                            ->schema([
                                Toggle::make('evaporative_filter')->live(),
                                LabelService::sectionMake('air_purifiers', 'Filtr ewaporacyjny')
                                    ->schema([
                                        TextInput::make('evaporative_filter_life')
                                            ->numeric(),
                                        TextInput::make('evaporative_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn (callable $get) => $get('evaporative_filter')),

                                Toggle::make('hepa_filter')->live(),
                                LabelService::sectionMake('air_purifiers', 'Filtr HEPA')
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
                                LabelService::sectionMake('air_purifiers', 'Filtr węglowy')
                                    ->schema([
                                        TextInput::make('carbon_filter_service_life')
                                            ->numeric(),
                                        TextInput::make('carbon_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn (callable $get) => $get('carbon_filter')),

                                Toggle::make('mesh_filter')
                                    ->label('Filtr wstępny'),

                                Textarea::make('filter_costs'),
                            ]),

                        Tab::make(LabelService::tab('air_purifiers', 'Funkcje'))
                            ->schema([
                                Toggle::make('ionization')->live(),
                                LabelService::sectionMake('air_purifiers', 'Jonizator')
                                    ->schema([
                                        Select::make('ionizer_type')
                                            ->label('Typ Jonizatora')
                                            ->options(IonizerType::getOptions()),
                                        Toggle::make('ionizer_switch'),
                                    ])
                                    ->visible(fn (callable $get) => $get('ionization')),

                                LabelService::sectionMake('air_purifiers', 'Inne funkcje')
                                    ->schema([
                                        Toggle::make('uvc'),

                                        Toggle::make('mobile_app'),

                                        Toggle::make('remote_control'),

                                        Select::make('productFunctions')
                                            ->label('Funkcje i wyposażenie')
                                            ->relationship('productFunctions', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nazwa')
                                                    ->required(),
                                            ])
                                            ->columnSpanFull(),

                                        Toggle::make('heating_and_cooling_function'),

                                        Toggle::make('cooling_function'),
                                    ])->collapsible(),

                                LabelService::sectionMake('air_purifiers', 'Czujniki')
                                    ->schema([
                                        Toggle::make('pm2_sensor'),

                                        Toggle::make('lzo_tvcop_sensor'),

                                        Toggle::make('temperature_sensor'),

                                        Toggle::make('humidity_sensor'),

                                        Toggle::make('light_sensor'),
                                    ])->collapsible(),

                                Select::make('certificates')
                                    ->label('Certyfikaty')
                                    ->multiple()
                                    ->searchable()
                                    ->options([
                                        'ECARF' => 'ECARF',
                                        'Allergy UK' => 'Allergy UK',
                                        'AHAM' => 'AHAM',
                                        'TÜV' => 'TÜV',
                                        'CE' => 'CE',
                                    ])
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nazwa certyfikatu')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(fn (array $data): string => $data['name']),
                            ]),

                        Tab::make(LabelService::tab('air_purifiers', 'Wymiary'))
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

                        Tab::make(LabelService::tab('air_purifiers', 'Klasyfikacja'))
                            ->schema([
                                TagsInput::make('type_of_device')
                                    ->placeholder('Dodaj typ urządzenia')
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

                        Tab::make(LabelService::tab('air_purifiers', 'Daty'))
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
                            )
                            ->visible(fn () => count($customFieldSchema) > 0),
        ];

        return $schema
            ->components([
                FormFieldSearch::make(),
                Tabs::make('Air Purifier Form')
                    ->tabs(FormLayoutService::applyLayout('air_purifiers', $defaultTabs))
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
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
                        'filters' => [
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

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
