<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Imports\AirConditionerImporter;
use App\Filament\Resources\AirConditionerResource\Pages\CreateAirConditioner;
use App\Filament\Resources\AirConditionerResource\Pages\EditAirConditioner;
use App\Filament\Resources\AirConditionerResource\Pages\ListAirConditioners;
use App\Models\AirConditioner;
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
use Filament\Tables\Table;
use UnitEnum;

final class AirConditionerResource extends Resource
{
    protected static ?string $model = AirConditioner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Klimatyzatory';

    protected static ?string $pluralLabel = 'Klimatyzatory';

    protected static ?string $label = 'Klimatyzator';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'model';

    public static function getFieldDefinitions(): array
    {
        return [
            'status' => fn () => Select::make('status')
                ->selectablePlaceholder(false)
                ->options([
                    'draft' => 'Szkic',
                    'published' => 'Opublikowany',
                    'archived' => 'Zarchiwizowany',
                ])
                ->required()
                ->label('Status'),

            'model' => fn () => TextInput::make('model')
                ->required()
                ->maxLength(255)
                ->label('Model'),

            'brand_name' => fn () => TextInput::make('brand_name')
                ->required()
                ->maxLength(255)
                ->label('Marka'),

            'type' => fn () => Select::make('type')
                ->label('Typ')
                ->options([
                    'przenosny' => 'Przenośny',
                    'split' => 'Split',
                    'multisplit' => 'Multisplit',
                    'monoblok' => 'Monoblok',
                    'okienny' => 'Okienny',
                ])
                ->searchable(),

            'price' => fn () => TextInput::make('price')
                ->numeric()
                ->prefix('PLN')
                ->label('Cena'),

            'price_before' => fn () => TextInput::make('price_before')
                ->numeric()
                ->prefix('PLN')
                ->label('Cena przed'),

            'image' => fn () => TextInput::make('image')
                ->disabled(),

            'discount_info' => fn () => Textarea::make('discount_info')
                ->label('Informacje o zniżce')
                ->columnSpanFull(),

            'partner_name' => fn () => TextInput::make('partner_name')
                ->label('Nazwa partnera'),

            'partner_link_url' => fn () => Textarea::make('partner_link_url')
                ->label('Link partnerski')
                ->columnSpanFull(),

            'partner_link_rel_2' => fn () => Select::make('partner_link_rel_2')
                ->multiple()
                ->options([
                    'nofollow' => 'nofollow',
                    'dofollow' => 'dofollow',
                    'sponsored' => 'sponsored',
                    'noopener' => 'noopener',
                ])
                ->label('Atrybuty rel partnera'),

            'partner_link_title' => fn () => TextInput::make('partner_link_title')
                ->label('Tytuł linku partnera'),

            'ceneo_url' => fn () => Textarea::make('ceneo_url')
                ->label('Link Ceneo')
                ->columnSpanFull(),

            'ceneo_link_rel_2' => fn () => Select::make('ceneo_link_rel_2')
                ->multiple()
                ->options([
                    'nofollow' => 'nofollow',
                    'dofollow' => 'dofollow',
                    'sponsored' => 'sponsored',
                    'noopener' => 'noopener',
                ])
                ->label('Atrybuty rel Ceneo'),

            'ceneo_link_title' => fn () => TextInput::make('ceneo_link_title')
                ->label('Tytuł linku Ceneo'),

            'review_link' => fn () => Textarea::make('review_link')
                ->label('Link do recenzji')
                ->columnSpanFull(),

            'gallery' => fn () => FileUpload::make('gallery')
                ->label('Galeria zdjęć')
                ->directory('air-conditioners')
                ->image()
                ->multiple()
                ->columnSpanFull(),

            'maximum_cooling_power' => fn () => TextInput::make('maximum_cooling_power')
                ->numeric()
                ->suffix('BTU/h')
                ->label('Maksymalna moc chłodzenia'),

            'max_cooling_area_manufacturer' => fn () => TextInput::make('max_cooling_area_manufacturer')
                ->numeric()
                ->suffix('m²')
                ->label('Maks. powierzchnia chłodzenia (producent)'),

            'max_cooling_area_ro' => fn () => TextInput::make('max_cooling_area_ro')
                ->numeric()
                ->suffix('m²')
                ->label('Maks. powierzchnia chłodzenia (RO)'),

            'max_cooling_temperature' => fn () => TextInput::make('max_cooling_temperature')
                ->numeric()
                ->suffix('°C')
                ->label('Maksymalna temperatura chłodzenia'),

            'min_cooling_temperature' => fn () => TextInput::make('min_cooling_temperature')
                ->numeric()
                ->suffix('°C')
                ->label('Minimalna temperatura chłodzenia'),

            'cooling_energy_class' => fn () => TextInput::make('cooling_energy_class')
                ->label('Klasa energetyczna chłodzenia'),

            'eer' => fn () => TextInput::make('eer')
                ->numeric()
                ->label('EER (Efektywność energetyczna)'),

            'rated_power_cooling_consumption' => fn () => TextInput::make('rated_power_cooling_consumption')
                ->numeric()
                ->suffix('W')
                ->label('Zużycie energii przy chłodzeniu'),

            'mode_cooling' => fn () => Toggle::make('mode_cooling')
                ->label('Tryb chłodzenia'),

            'maximum_heating_power' => fn () => TextInput::make('maximum_heating_power')
                ->numeric()
                ->suffix('BTU/h')
                ->label('Maksymalna moc grzania'),

            'max_heating_area_manufacturer' => fn () => TextInput::make('max_heating_area_manufacturer')
                ->numeric()
                ->suffix('m²')
                ->label('Maks. powierzchnia grzania (producent)'),

            'max_heating_area_ro' => fn () => TextInput::make('max_heating_area_ro')
                ->numeric()
                ->suffix('m²')
                ->label('Maks. powierzchnia grzania (RO)'),

            'max_heating_temperature' => fn () => TextInput::make('max_heating_temperature')
                ->numeric()
                ->suffix('°C')
                ->label('Maksymalna temperatura grzania'),

            'min_heating_temperature' => fn () => TextInput::make('min_heating_temperature')
                ->numeric()
                ->suffix('°C')
                ->label('Minimalna temperatura grzania'),

            'heating_energy_class' => fn () => TextInput::make('heating_energy_class')
                ->label('Klasa energetyczna grzania'),

            'cop' => fn () => TextInput::make('cop')
                ->numeric()
                ->label('COP (Współczynnik wydajności)'),

            'rated_power_heating_consumption' => fn () => TextInput::make('rated_power_heating_consumption')
                ->numeric()
                ->suffix('W')
                ->label('Zużycie energii przy grzaniu'),

            'mode_heating' => fn () => Toggle::make('mode_heating')
                ->label('Tryb grzania'),

            'mode_dry' => fn () => Toggle::make('mode_dry')
                ->label('Tryb osuszania'),

            'max_performance_dry' => fn () => TextInput::make('max_performance_dry')
                ->numeric()
                ->suffix('l/24h')
                ->visible(fn (callable $get) => $get('mode_dry'))
                ->label('Maksymalna wydajność osuszania'),

            'max_performance_dry_condition' => fn () => TextInput::make('max_performance_dry_condition')
                ->visible(fn (callable $get) => $get('mode_dry'))
                ->label('Warunki maksymalnej wydajności osuszania'),

            'mode_fan' => fn () => Toggle::make('mode_fan')
                ->label('Tryb wentylatora'),

            'mode_purify' => fn () => Toggle::make('mode_purify')
                ->label('Tryb oczyszczania'),

            'max_air_flow' => fn () => TextInput::make('max_air_flow')
                ->numeric()
                ->suffix('m³/h')
                ->label('Maksymalny przepływ powietrza'),

            'number_of_fan_speeds' => fn () => TextInput::make('number_of_fan_speeds')
                ->numeric()
                ->label('Liczba prędkości wentylatora'),

            'swing' => fn () => TextInput::make('swing')
                ->label('Swing (kierowanie powietrzem)'),

            'temperature_range' => fn () => TextInput::make('temperature_range')
                ->label('Zakres temperatur'),

            'max_loudness' => fn () => TextInput::make('max_loudness')
                ->numeric()
                ->suffix('dB')
                ->label('Maksymalny poziom hałasu'),

            'min_loudness' => fn () => TextInput::make('min_loudness')
                ->numeric()
                ->suffix('dB')
                ->label('Minimalny poziom hałasu'),

            'mesh_filter' => fn () => Toggle::make('mesh_filter')
                ->label('Filtr wstępny'),

            'hepa_filter' => fn () => Toggle::make('hepa_filter')
                ->live()
                ->label('Filtr HEPA'),

            'hepa_filter_price' => fn () => TextInput::make('hepa_filter_price')
                ->numeric()
                ->prefix('PLN')
                ->visible(fn (callable $get) => $get('hepa_filter'))
                ->label('Cena filtra HEPA'),

            'hepa_service_life' => fn () => TextInput::make('hepa_service_life')
                ->numeric()
                ->suffix('miesięcy')
                ->visible(fn (callable $get) => $get('hepa_filter'))
                ->label('Żywotność filtra HEPA'),

            'carbon_filter' => fn () => Toggle::make('carbon_filter')
                ->live()
                ->label('Filtr węglowy'),

            'carbon_filter_price' => fn () => TextInput::make('carbon_filter_price')
                ->numeric()
                ->prefix('PLN')
                ->visible(fn (callable $get) => $get('carbon_filter'))
                ->label('Cena filtra węglowego'),

            'carbon_service_life' => fn () => TextInput::make('carbon_service_life')
                ->numeric()
                ->suffix('miesięcy')
                ->visible(fn (callable $get) => $get('carbon_filter'))
                ->label('Żywotność filtra węglowego'),

            'ionization' => fn () => Toggle::make('ionization')
                ->label('Jonizacja'),

            'uvc' => fn () => Toggle::make('uvc')
                ->live()
                ->label('Lampa UV-C'),

            'uv_light_generator' => fn () => Textarea::make('uv_light_generator')
                ->visible(fn (callable $get) => $get('uvc'))
                ->label('Generator światła UV')
                ->columnSpanFull(),

            'remote_control' => fn () => Toggle::make('remote_control')
                ->label('Pilot zdalnego sterowania'),

            'mobile_app' => fn () => Toggle::make('mobile_app')
                ->label('Aplikacja mobilna'),

            'productFunctions' => fn () => Select::make('productFunctions')
                ->label('Funkcje')
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

            'functions_and_equipment_condi' => fn () => TagsInput::make('functions_and_equipment_condi')
                ->label('Funkcje i wyposażenie klimatyzatora')
                ->columnSpanFull(),

            'refrigerant_kind' => fn () => TextInput::make('refrigerant_kind')
                ->label('Rodzaj chłodziwa'),

            'refrigerant_amount' => fn () => TextInput::make('refrigerant_amount')
                ->numeric()
                ->suffix('kg')
                ->label('Ilość chłodziwa'),

            'needs_to_be_completed' => fn () => TextInput::make('needs_to_be_completed')
                ->label('Wymaga uzupełnienia'),

            'rated_voltage' => fn () => TextInput::make('rated_voltage')
                ->numeric()
                ->suffix('V')
                ->label('Napięcie znamionowe'),

            'width' => fn () => TextInput::make('width')
                ->numeric()
                ->suffix('cm')
                ->label('Szerokość'),

            'height' => fn () => TextInput::make('height')
                ->numeric()
                ->suffix('cm')
                ->label('Wysokość'),

            'depth' => fn () => TextInput::make('depth')
                ->numeric()
                ->suffix('cm')
                ->label('Głębokość'),

            'weight' => fn () => TextInput::make('weight')
                ->numeric()
                ->suffix('kg')
                ->label('Waga'),

            'discharge_pipe' => fn () => Toggle::make('discharge_pipe')
                ->live()
                ->label('Rura odprowadzająca'),

            'discharge_pipe_length' => fn () => TextInput::make('discharge_pipe_length')
                ->numeric()
                ->suffix('m')
                ->visible(fn (callable $get) => $get('discharge_pipe'))
                ->label('Długość rury odprowadzającej'),

            'discharge_pipe_diameter' => fn () => TextInput::make('discharge_pipe_diameter')
                ->numeric()
                ->suffix('mm')
                ->visible(fn (callable $get) => $get('discharge_pipe'))
                ->label('Średnica rury odprowadzającej'),

            'drain_hose' => fn () => Toggle::make('drain_hose')
                ->label('Wąż odprowadzający'),

            'sealing' => fn () => TextInput::make('sealing')
                ->label('Uszczelnienie'),

            'colors' => fn () => TagsInput::make('colors')
                ->label('Dostępne kolory')
                ->columnSpanFull(),

            'capability' => fn () => TextInput::make('capability')
                ->numeric()
                ->label('Ocena możliwości'),

            'profitability' => fn () => TextInput::make('profitability')
                ->numeric()
                ->label('Ocena opłacalności'),

            'ranking' => fn () => TextInput::make('ranking')
                ->numeric()
                ->label('Pozycja w rankingu'),

            'ranking_hidden' => fn () => Toggle::make('ranking_hidden')
                ->label('Ukryj w rankingu'),

            'main_ranking' => fn () => Toggle::make('main_ranking')
                ->label('Główny ranking'),

            'small' => fn () => TextInput::make('small')
                ->label('Mały'),

            'manual' => fn () => FileUpload::make('manual')
                ->directory('instructions')
                ->label('Instrukcja obsługi'),

            'remote_id' => fn () => TextInput::make('remote_id')
                ->numeric()
                ->label('ID zewnętrzne'),

            'sort' => fn () => TextInput::make('sort')
                ->numeric()
                ->label('Kolejność sortowania'),

            'user_created' => fn () => TextInput::make('user_created')
                ->label('Utworzony przez'),

            'date_created' => fn () => DateTimePicker::make('date_created')
                ->label('Data utworzenia'),

            'user_updated' => fn () => TextInput::make('user_updated')
                ->label('Zaktualizowany przez'),

            'date_updated' => fn () => DateTimePicker::make('date_updated')
                ->label('Data aktualizacji'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_conditioners');

        return $schema
            ->components([
                Tabs::make('Formularz Klimatyzatora')
                    ->columnSpanFull()
                    ->tabs(FormLayoutService::buildForm('air_conditioners', static::getFieldDefinitions(), $customFieldSchema)),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = CustomFieldService::getTableColumns('air_conditioners');

        return $table
            ->columns($availableColumns)
            ->filters([])
            ->headerActions([
                ImportAction::make()
                    ->importer(AirConditionerImporter::class),
                ExportActionService::createExportAllAction('air_conditioners'),
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'air_conditioners',
                            ],
                        ],
                    ])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('air_conditioners'),
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
            'index' => ListAirConditioners::route('/'),
            'create' => CreateAirConditioner::route('/create'),
            'edit' => EditAirConditioner::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::count();
    }
}
