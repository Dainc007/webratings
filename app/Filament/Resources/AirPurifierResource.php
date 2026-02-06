<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\IonizerType;
use App\Enums\Status;
use App\Filament\Imports\AirPurifierImporter;
use App\Filament\Resources\AirPurifierResource\Pages\CreateAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\EditAirPurifier;
use App\Filament\Resources\AirPurifierResource\Pages\ListAirPurifiers;
use App\Models\AirPurifier;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
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

    public static function getFieldDefinitions(): array
    {
        return [
            'status' => fn () => Select::make('status')
                ->default('draft')
                ->selectablePlaceholder(false)
                ->options(Status::getOptions()),

            'model' => fn () => TextInput::make('model')
                ->maxLength(255),

            'brand_name' => fn () => TextInput::make('brand_name')
                ->maxLength(255),

            'price' => fn () => TextInput::make('price')
                ->numeric()
                ->prefix('zł'),

            'price_before' => fn () => TextInput::make('price_before')
                ->numeric()
                ->prefix('zł')
                ->label('Cena przed'),

            'discount_info' => fn () => Textarea::make('discount_info')
                ->label('Informacje o zniżce')
                ->columnSpanFull(),

            'price_date' => fn () => DateTimePicker::make('price_date')
                ->default(now())
                ->seconds(false),

            'capability_points' => fn () => TextInput::make('capability_points')
                ->numeric()
                ->nullable()
                ->label('Punkty za możliwości'),

            'profitability_points' => fn () => TextInput::make('profitability_points')
                ->numeric()
                ->nullable()
                ->label('Punkty za opłacalność'),

            'popularity' => fn () => TextInput::make('popularity')
                ->numeric()
                ->nullable()
                ->label('Popularność'),

            'partner_link_url' => fn () => TextInput::make('partner_link_url')
                ->maxLength(255)
                ->label('Partner Link URL'),

            'partner_link_rel_2' => fn () => Select::make('partner_link_rel_2')
                ->multiple()
                ->options([
                    'nofollow' => 'nofollow',
                    'dofollow' => 'dofollow',
                    'sponsored' => 'sponsored',
                    'noopener' => 'noopener',
                ])
                ->label('Partner Link Rel Attributes'),

            'ceneo_url' => fn () => TextInput::make('ceneo_url')
                ->maxLength(255)
                ->label('Ceneo URL'),

            'ceneo_link_rel_2' => fn () => Select::make('ceneo_link_rel_2')
                ->multiple()
                ->options([
                    'nofollow' => 'nofollow',
                    'dofollow' => 'dofollow',
                    'sponsored' => 'sponsored',
                    'noopener' => 'noopener',
                ])
                ->label('Ceneo Link Rel Attributes'),

            'review_link' => fn () => TextInput::make('review_link')
                ->maxLength(255)
                ->label('Link do recenzji'),

            'gallery' => fn () => FileUpload::make('gallery')
                ->label('Galeria zdjęć')
                ->directory('air-purifiers')
                ->image()
                ->multiple()
                ->columnSpanFull(),

            'max_performance' => fn () => TextInput::make('max_performance')
                ->numeric()
                ->minValue(0),

            'max_area' => fn () => TextInput::make('max_area')
                ->numeric()
                ->minValue(0),

            'max_area_ro' => fn () => TextInput::make('max_area_ro')
                ->numeric()
                ->minValue(0),

            'number_of_fan_speeds' => fn () => TextInput::make('number_of_fan_speeds')
                ->numeric()
                ->minValue(0)
                ->label('Liczba prędkości oczyszczania'),

            'min_loudness' => fn () => TextInput::make('min_loudness')
                ->numeric()
                ->minValue(0),

            'max_loudness' => fn () => TextInput::make('max_loudness')
                ->numeric()
                ->minValue(0),

            'min_rated_power_consumption' => fn () => TextInput::make('min_rated_power_consumption')
                ->numeric()
                ->minValue(0)
                ->label('Min. pobór prądu'),

            'max_rated_power_consumption' => fn () => TextInput::make('max_rated_power_consumption')
                ->numeric()
                ->minValue(0)
                ->label('Max. pobór prądu'),

            'has_humidification' => fn () => Toggle::make('has_humidification')
                ->label('Posiada nawilżanie')
                ->live(),

            'humidification_type' => fn () => Select::make('humidification_type')
                ->label('Typ nawilżania')
                ->options([
                    'vapor' => 'Parowe',
                    'ultrasonic' => 'Ultradźwiękowe',
                    'evaporative' => 'Ewaporacyjne',
                ])
                ->visible(fn (callable $get) => $get('has_humidification')),

            'humidification_switch' => fn () => Toggle::make('humidification_switch')
                ->label('Możliwość wyłączenia')
                ->visible(fn (callable $get) => $get('has_humidification')),

            'humidification_area' => fn () => TextInput::make('humidification_area')
                ->numeric()
                ->minValue(0)
                ->nullable()
                ->label('Powierzchnia nawilżania')
                ->suffix('m²')
                ->visible(fn (callable $get) => $get('has_humidification')),

            'water_tank_capacity' => fn () => TextInput::make('water_tank_capacity')
                ->numeric()
                ->minValue(0)
                ->label('Pojemność zbiornika na wodę')
                ->suffix('l')
                ->visible(fn (callable $get) => $get('has_humidification')),

            'humidification_efficiency' => fn () => TextInput::make('humidification_efficiency')
                ->numeric()
                ->minValue(0)
                ->label('Wydajność nawilżania')
                ->suffix('ml/h')
                ->visible(fn (callable $get) => $get('has_humidification')),

            'hygrometer' => fn () => Toggle::make('hygrometer')
                ->label('Higrometr'),

            'hygrostat' => fn () => Toggle::make('hygrostat')
                ->label('Higrostat')
                ->live(),

            'hygrostat_min' => fn () => TextInput::make('hygrostat_min')
                ->numeric()
                ->minValue(0)
                ->label('Higrostat min')
                ->suffix('%')
                ->visible(fn (callable $get) => $get('hygrostat')),

            'hygrostat_max' => fn () => TextInput::make('hygrostat_max')
                ->numeric()
                ->minValue(0)
                ->label('Higrostat max')
                ->suffix('%')
                ->visible(fn (callable $get) => $get('hygrostat')),

            'evaporative_filter' => fn () => Toggle::make('evaporative_filter')
                ->live(),

            'evaporative_filter_life' => fn () => TextInput::make('evaporative_filter_life')
                ->numeric()
                ->visible(fn (callable $get) => $get('evaporative_filter')),

            'evaporative_filter_price' => fn () => TextInput::make('evaporative_filter_price')
                ->numeric()
                ->visible(fn (callable $get) => $get('evaporative_filter')),

            'hepa_filter' => fn () => Toggle::make('hepa_filter')
                ->live(),

            'hepa_filter_class' => fn () => TextInput::make('hepa_filter_class')
                ->visible(fn (callable $get) => $get('hepa_filter')),

            'effectiveness_hepa_filter' => fn () => TextInput::make('effectiveness_hepa_filter')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->visible(fn (callable $get) => $get('hepa_filter')),

            'hepa_filter_service_life' => fn () => TextInput::make('hepa_filter_service_life')
                ->numeric()
                ->visible(fn (callable $get) => $get('hepa_filter')),

            'hepa_filter_price' => fn () => TextInput::make('hepa_filter_price')
                ->numeric()
                ->visible(fn (callable $get) => $get('hepa_filter')),

            'carbon_filter' => fn () => Toggle::make('carbon_filter')
                ->live(),

            'carbon_filter_service_life' => fn () => TextInput::make('carbon_filter_service_life')
                ->numeric()
                ->visible(fn (callable $get) => $get('carbon_filter')),

            'carbon_filter_price' => fn () => TextInput::make('carbon_filter_price')
                ->numeric()
                ->visible(fn (callable $get) => $get('carbon_filter')),

            'mesh_filter' => fn () => Toggle::make('mesh_filter')
                ->label('Filtr wstępny'),

            'filter_costs' => fn () => Textarea::make('filter_costs'),

            'ionization' => fn () => Toggle::make('ionization')
                ->live(),

            'ionizer_type' => fn () => Select::make('ionizer_type')
                ->label('Typ Jonizatora')
                ->options(IonizerType::getOptions())
                ->visible(fn (callable $get) => $get('ionization')),

            'ionizer_switch' => fn () => Toggle::make('ionizer_switch')
                ->visible(fn (callable $get) => $get('ionization')),

            'uvc' => fn () => Toggle::make('uvc'),

            'mobile_app' => fn () => Toggle::make('mobile_app'),

            'remote_control' => fn () => Toggle::make('remote_control'),

            'functions_and_equipment' => fn () => TagsInput::make('functions_and_equipment')
                ->placeholder('Dodaj funkcję')
                ->separator(','),

            'heating_and_cooling_function' => fn () => Toggle::make('heating_and_cooling_function'),

            'cooling_function' => fn () => Toggle::make('cooling_function'),

            'pm2_sensor' => fn () => Toggle::make('pm2_sensor'),

            'lzo_tvcop_sensor' => fn () => Toggle::make('lzo_tvcop_sensor'),

            'temperature_sensor' => fn () => Toggle::make('temperature_sensor'),

            'humidity_sensor' => fn () => Toggle::make('humidity_sensor'),

            'light_sensor' => fn () => Toggle::make('light_sensor'),

            'certificates' => fn () => TagsInput::make('certificates')
                ->placeholder('Dodaj certyfikat')
                ->separator(','),

            'width' => fn () => TextInput::make('width')
                ->numeric()
                ->nullable(),

            'height' => fn () => TextInput::make('height')
                ->numeric()
                ->nullable(),

            'depth' => fn () => TextInput::make('depth')
                ->numeric()
                ->nullable(),

            'weight' => fn () => TextInput::make('weight')
                ->numeric()
                ->step(0.1),

            'colors' => fn () => TagsInput::make('colors')
                ->columnSpanFull()
                ->placeholder('Dodaj kolor')
                ->separator(','),

            'type_of_device' => fn () => TagsInput::make('type_of_device')
                ->placeholder('Dodaj typ urządzenia')
                ->separator(','),

            'main_ranking' => fn () => Toggle::make('main_ranking'),

            'ranking_hidden' => fn () => Toggle::make('ranking_hidden'),

            'for_kids' => fn () => Toggle::make('for_kids'),

            'bedroom' => fn () => Toggle::make('bedroom'),

            'smokers' => fn () => Toggle::make('smokers'),

            'office' => fn () => Toggle::make('office'),

            'kindergarten' => fn () => Toggle::make('kindergarten'),

            'astmatic' => fn () => Toggle::make('astmatic'),

            'alergic' => fn () => Toggle::make('alergic'),

            'date_created' => fn () => DateTimePicker::make('date_created')
                ->disabled(),

            'date_updated' => fn () => DateTimePicker::make('date_updated')
                ->disabled(),

            'created_at' => fn () => DateTimePicker::make('created_at')
                ->disabled(),

            'updated_at' => fn () => DateTimePicker::make('updated_at')
                ->disabled(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_purifiers');

        return $schema
            ->components([
                Tabs::make('Air Purifier Form')
                    ->tabs(FormLayoutService::buildForm('air_purifiers', static::getFieldDefinitions(), $customFieldSchema))
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

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
