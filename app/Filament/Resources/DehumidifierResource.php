<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DehumidifierType;
use App\Filament\Imports\DehumidifierImporter;
use App\Filament\Resources\DehumidifierResource\Pages\CreateDehumidifier;
use App\Filament\Resources\DehumidifierResource\Pages\EditDehumidifier;
use App\Filament\Resources\DehumidifierResource\Pages\ListDehumidifiers;
use App\Models\Dehumidifier;
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

final class DehumidifierResource extends Resource
{
    protected static ?string $model = Dehumidifier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-eye-dropper';

    protected static ?string $navigationLabel = 'Osuszacze Powietrza';

    protected static ?string $pluralLabel = 'Osuszacze Powietrza';

    protected static ?string $label = 'Osuszacz Powietrza';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 5;

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
                ->options(DehumidifierType::getOptions())
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

            'max_performance_dry' => fn () => TextInput::make('max_performance_dry')
                ->numeric()
                ->suffix('l/24h')
                ->label('Maksymalna wydajność osuszania'),

            'other_performance_dry' => fn () => TextInput::make('other_performance_dry')
                ->numeric()
                ->suffix('l/24h')
                ->label('Inna wydajność osuszania'),

            'max_performance_dry_condition' => fn () => TextInput::make('max_performance_dry_condition')
                ->label('Warunki maksymalnej wydajności'),

            'other_performance_condition' => fn () => TextInput::make('other_performance_condition')
                ->label('Warunki innej wydajności'),

            'max_drying_area_manufacturer' => fn () => TextInput::make('max_drying_area_manufacturer')
                ->numeric()
                ->suffix('m²')
                ->label('Maks. powierzchnia osuszania (producent)'),

            'max_drying_area_ro' => fn () => TextInput::make('max_drying_area_ro')
                ->numeric()
                ->suffix('m²')
                ->label('Maks. powierzchnia osuszania (RO)'),

            'minimum_temperature' => fn () => TextInput::make('minimum_temperature')
                ->numeric()
                ->suffix('°C')
                ->label('Minimalna temperatura'),

            'maximum_temperature' => fn () => TextInput::make('maximum_temperature')
                ->numeric()
                ->suffix('°C')
                ->label('Maksymalna temperatura'),

            'minimum_humidity' => fn () => TextInput::make('minimum_humidity')
                ->numeric()
                ->suffix('%')
                ->label('Minimalna wilgotność'),

            'maximum_humidity' => fn () => TextInput::make('maximum_humidity')
                ->numeric()
                ->suffix('%')
                ->label('Maksymalna wilgotność'),

            'water_tank_capacity' => fn () => TextInput::make('water_tank_capacity')
                ->numeric()
                ->suffix('l')
                ->label('Pojemność zbiornika na wodę'),

            'minimum_fill_time' => fn () => TextInput::make('minimum_fill_time')
                ->numeric()
                ->suffix('h')
                ->label('Minimalny czas napełniania'),

            'average_filling_time' => fn () => TextInput::make('average_filling_time')
                ->numeric()
                ->suffix('h')
                ->label('Średni czas napełniania'),

            'higrostat' => fn () => Toggle::make('higrostat')
                ->label('Higrostat')
                ->live()
                ->columnSpanFull(),

            'min_value_for_hygrostat' => fn () => TextInput::make('min_value_for_hygrostat')
                ->numeric()
                ->suffix('%')
                ->label('Minimalna wartość higrostatu')
                ->visible(fn (callable $get) => $get('higrostat')),

            'max_value_for_hygrostat' => fn () => TextInput::make('max_value_for_hygrostat')
                ->numeric()
                ->suffix('%')
                ->label('Maksymalna wartość higrostatu')
                ->visible(fn (callable $get) => $get('higrostat')),

            'increment_of_the_hygrostat' => fn () => TextInput::make('increment_of_the_hygrostat')
                ->label('Skok higrostatu')
                ->visible(fn (callable $get) => $get('higrostat')),

            'number_of_fan_speeds' => fn () => TextInput::make('number_of_fan_speeds')
                ->numeric()
                ->label('Liczba prędkości wentylatora'),

            'max_air_flow' => fn () => TextInput::make('max_air_flow')
                ->numeric()
                ->suffix('m³/h')
                ->label('Maksymalny przepływ powietrza'),

            'max_loudness' => fn () => TextInput::make('max_loudness')
                ->numeric()
                ->suffix('dB')
                ->label('Maksymalny poziom hałasu'),

            'min_loudness' => fn () => TextInput::make('min_loudness')
                ->numeric()
                ->suffix('dB')
                ->label('Minimalny poziom hałasu'),

            'modes_of_operation' => fn () => TagsInput::make('modes_of_operation')
                ->label('Tryby pracy')
                ->columnSpanFull(),

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

            'uv_light_generator' => fn () => Toggle::make('uv_light_generator')
                ->visible(fn (callable $get) => $get('uvc'))
                ->label('Generator światła UV'),

            'mobile_app' => fn () => Toggle::make('mobile_app')
                ->live()
                ->label('Aplikacja mobilna'),

            'mobile_features' => fn () => TagsInput::make('mobile_features')
                ->visible(fn (callable $get) => $get('mobile_app'))
                ->label('Funkcje aplikacji mobilnej')
                ->columnSpanFull(),

            'productFunctions' => fn () => Select::make('productFunctions')
                ->relationship('productFunctions', 'name')
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nazwa')
                        ->required(),
                ])
                ->label('Funkcje')
                ->multiple()
                ->columnSpanFull(),

            'refrigerant_kind' => fn () => TextInput::make('refrigerant_kind')
                ->label('Rodzaj chłodziwa'),

            'refrigerant_amount' => fn () => TextInput::make('refrigerant_amount')
                ->numeric()
                ->suffix('kg')
                ->label('Ilość chłodziwa'),

            'needs_to_be_completed' => fn () => TextInput::make('needs_to_be_completed')
                ->label('Wymaga uzupełnienia'),

            'rated_power_consumption' => fn () => TextInput::make('rated_power_consumption')
                ->numeric()
                ->suffix('W')
                ->label('Zużycie energii'),

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

            'gallery' => fn () => FileUpload::make('gallery')
                ->label('Galeria zdjęć')
                ->directory('dehumidifiers')
                ->image(),

            'manual_file' => fn () => FileUpload::make('manual_file')
                ->directory('instructions')
                ->label('Plik instrukcji'),

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
        $customFieldSchema = CustomFieldService::getFormFields('dehumidifiers');

        return $schema
            ->components([
                Tabs::make('Formularz Osuszacza')
                    ->columnSpanFull()
                    ->tabs(FormLayoutService::buildForm('dehumidifiers', static::getFieldDefinitions(), $customFieldSchema)),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = CustomFieldService::getTableColumns('dehumidifiers');

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->filters([])
            ->headerActions([
                ImportAction::make('Import Dehumidifiers')
                    ->importer(DehumidifierImporter::class),
                ExportActionService::createExportAllAction('dehumidifiers'),
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'dehumidifiers',
                            ],
                        ],
                    ])),
            ])

            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('dehumidifiers'),
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
            'index' => ListDehumidifiers::route('/'),
            'create' => CreateDehumidifier::route('/create'),
            'edit' => EditDehumidifier::route('/{record}/edit'),
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
