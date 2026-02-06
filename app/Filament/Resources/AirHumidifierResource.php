<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Imports\AirHumidifierImporter;
use App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\EditAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\ListAirHumidifiers;
use App\Models\AirHumidifier;
use App\Services\CustomFieldService;
use App\Services\ExportActionService;
use App\Services\FormLayoutService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
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

final class AirHumidifierResource extends Resource
{
    protected static ?string $model = AirHumidifier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Nawilżacze Powietrza';

    protected static ?string $pluralLabel = 'Nawilżacze Powietrza';

    protected static ?string $label = 'Nawilżacze Powietrza';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'model';

    public static function getFieldDefinitions(): array
    {
        return [
            'remote_id' => fn () => TextInput::make('remote_id')
                ->label('ID zdalne'),

            'status' => fn () => TextInput::make('status')
                ->label('Status'),

            'model' => fn () => TextInput::make('model')
                ->label('Model'),

            'brand_name' => fn () => TextInput::make('brand_name')
                ->label('Marka'),

            'price' => fn () => TextInput::make('price')
                ->numeric()
                ->label('Cena'),

            'price_before' => fn () => TextInput::make('price_before')
                ->numeric()
                ->label('Cena przed'),

            'discount_info' => fn () => Textarea::make('discount_info')
                ->label('Informacje o zniżce')
                ->columnSpanFull(),

            'partner_link_url' => fn () => TextInput::make('partner_link_url')
                ->url()
                ->label('Link partnerski'),

            'ceneo_url' => fn () => TextInput::make('ceneo_url')
                ->url()
                ->label('Link Ceneo'),

            'review_link' => fn () => TextInput::make('review_link')
                ->url()
                ->label('Link do recenzji'),

            'capability' => fn () => TextInput::make('capability')
                ->label('Możliwości'),

            'ranking' => fn () => TextInput::make('ranking')
                ->label('Ranking'),

            'profitability' => fn () => TextInput::make('profitability')
                ->label('Opłacalność'),

            'ranking_hidden' => fn () => Toggle::make('ranking_hidden')
                ->label('Ukryj w rankingu'),

            'main_ranking' => fn () => Toggle::make('main_ranking')
                ->label('Ranking główny'),

            'types' => fn () => Select::make('types')
                ->label('Typy produktu')
                ->relationship('types', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nazwa')
                        ->required(),
                ])
                ->columnSpanFull(),

            'type_of_device' => fn () => Select::make('type_of_device')
                ->label('Typ urządzenia')
                ->options([
                    'ultradźwiękowy' => 'Ultradźwiękowy',
                    'ewaporacyjny' => 'Ewaporacyjny',
                    'parowy' => 'Parowy',
                ])
                ->searchable(),

            'max_performance' => fn () => TextInput::make('max_performance')
                ->numeric()
                ->label('Maksymalna wydajność'),

            'max_area' => fn () => TextInput::make('max_area')
                ->numeric()
                ->label('Maksymalna powierzchnia'),

            'max_area_ro' => fn () => TextInput::make('max_area_ro')
                ->numeric()
                ->label('Maksymalna powierzchnia RO'),

            'tested_efficiency' => fn () => TextInput::make('tested_efficiency')
                ->numeric()
                ->label('Wydajność testowana'),

            'fan_volume' => fn () => Toggle::make('fan_volume')
                ->label('Głośność Wentylatora')
                ->live(),

            'min_fan_volume' => fn () => TextInput::make('min_fan_volume')
                ->visible(fn (callable $get) => $get('fan_volume'))
                ->hint('w decybelach (dB)')
                ->numeric()
                ->label('Min. głośność'),

            'max_fan_volume' => fn () => TextInput::make('max_fan_volume')
                ->hint('w decybelach (dB)')
                ->visible(fn (callable $get) => $get('fan_volume'))
                ->numeric()
                ->label('Max głośność'),

            'min_rated_power_consumption' => fn () => TextInput::make('min_rated_power_consumption')
                ->numeric()
                ->label('Minimalny pobór mocy'),

            'max_rated_power_consumption' => fn () => TextInput::make('max_rated_power_consumption')
                ->numeric()
                ->label('Maksymalny pobór mocy'),

            'water_tank_capacity' => fn () => TextInput::make('water_tank_capacity')
                ->numeric()
                ->label('Pojemność zbiornika na wodę'),

            'water_tank_min_time' => fn () => TextInput::make('water_tank_min_time')
                ->numeric()
                ->label('Minimalny czas pracy zbiornika'),

            'water_tank_fill_type' => fn () => TextInput::make('water_tank_fill_type')
                ->label('Typ napełniania zbiornika'),

            'hygrostat' => fn () => Toggle::make('hygrostat')
                ->live()
                ->label('Higrostat'),

            'hygrostat_min' => fn () => TextInput::make('hygrostat_min')
                ->visible(fn (callable $get) => $get('hygrostat'))
                ->numeric()
                ->label('Higrostat min'),

            'hygrostat_max' => fn () => TextInput::make('hygrostat_max')
                ->visible(fn (callable $get) => $get('hygrostat'))
                ->numeric()
                ->label('Higrostat max'),

            'timer' => fn () => Toggle::make('timer')
                ->live()
                ->label('Timer'),

            'timer_min' => fn () => TextInput::make('timer_min')
                ->visible(fn (callable $get) => $get('timer'))
                ->numeric()
                ->label('Timer min'),

            'timer_max' => fn () => TextInput::make('timer_max')
                ->visible(fn (callable $get) => $get('timer'))
                ->numeric()
                ->label('Timer max'),

            'auto_mode' => fn () => Toggle::make('auto_mode')
                ->label('Tryb automatyczny'),

            'night_mode' => fn () => Toggle::make('night_mode')
                ->live()
                ->label('Tryb nocny'),

            'night_mode_min' => fn () => TextInput::make('night_mode_min')
                ->visible(fn (callable $get) => $get('night_mode'))
                ->numeric()
                ->label('Tryb nocny min'),

            'night_mode_max' => fn () => TextInput::make('night_mode_max')
                ->visible(fn (callable $get) => $get('night_mode'))
                ->numeric()
                ->label('Tryb nocny max'),

            'child_lock' => fn () => Toggle::make('child_lock')
                ->live()
                ->label('Blokada rodzicielska'),

            'child_lock_min' => fn () => TextInput::make('child_lock_min')
                ->visible(fn (callable $get) => $get('child_lock'))
                ->numeric()
                ->label('Blokada rodzicielska min'),

            'child_lock_max' => fn () => TextInput::make('child_lock_max')
                ->visible(fn (callable $get) => $get('child_lock'))
                ->numeric()
                ->label('Blokada rodzicielska max'),

            'display' => fn () => Toggle::make('display')
                ->live()
                ->label('Wyświetlacz'),

            'display_min' => fn () => TextInput::make('display_min')
                ->visible(fn (callable $get) => $get('display'))
                ->numeric()
                ->label('Wyświetlacz min'),

            'display_max' => fn () => TextInput::make('display_max')
                ->visible(fn (callable $get) => $get('display'))
                ->numeric()
                ->label('Wyświetlacz max'),

            'remote_control' => fn () => Toggle::make('remote_control')
                ->live()
                ->label('Pilot'),

            'remote_control_min' => fn () => TextInput::make('remote_control_min')
                ->visible(fn (callable $get) => $get('remote_control'))
                ->numeric()
                ->label('Pilot min'),

            'remote_control_max' => fn () => TextInput::make('remote_control_max')
                ->visible(fn (callable $get) => $get('remote_control'))
                ->numeric()
                ->label('Pilot max'),

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

            'mobile_app' => fn () => Toggle::make('mobile_app')
                ->label('Aplikacja mobilna'),

            'mobile_features' => fn () => TagsInput::make('mobile_features')
                ->placeholder('Dodaj funkcję')
                ->separator(',')
                ->label('Funkcje aplikacji'),

            'evaporative_filter' => fn () => Toggle::make('evaporative_filter')
                ->live()
                ->label('Filtr ewaporacyjny'),

            'evaporative_filter_life' => fn () => TextInput::make('evaporative_filter_life')
                ->visible(fn (callable $get) => $get('evaporative_filter'))
                ->numeric()
                ->label('Żywotność filtra ewaporacyjnego'),

            'evaporative_filter_price' => fn () => TextInput::make('evaporative_filter_price')
                ->visible(fn (callable $get) => $get('evaporative_filter'))
                ->numeric()
                ->label('Cena filtra ewaporacyjnego'),

            'silver_ion' => fn () => Toggle::make('silver_ion')
                ->live()
                ->label('Srebrna jonizacja'),

            'silver_ion_life' => fn () => TextInput::make('silver_ion_life')
                ->visible(fn (callable $get) => $get('silver_ion'))
                ->numeric()
                ->label('Żywotność srebrnej jonizacji'),

            'silver_ion_price' => fn () => TextInput::make('silver_ion_price')
                ->visible(fn (callable $get) => $get('silver_ion'))
                ->numeric()
                ->label('Cena srebrnej jonizacji'),

            'ceramic_filter' => fn () => Toggle::make('ceramic_filter')
                ->live()
                ->label('Filtr ceramiczny'),

            'ceramic_filter_life' => fn () => TextInput::make('ceramic_filter_life')
                ->visible(fn (callable $get) => $get('ceramic_filter'))
                ->numeric()
                ->label('Żywotność filtra ceramicznego'),

            'ceramic_filter_price' => fn () => TextInput::make('ceramic_filter_price')
                ->visible(fn (callable $get) => $get('ceramic_filter'))
                ->numeric()
                ->label('Cena filtra ceramicznego'),

            'uv_lamp' => fn () => Toggle::make('uv_lamp')
                ->live()
                ->label('Lampa UV'),

            'ionization' => fn () => Toggle::make('ionization')
                ->live()
                ->label('Jonizacja'),

            'hepa_filter_class' => fn () => TextInput::make('hepa_filter_class')
                ->label('Klasa filtra HEPA'),

            'mesh_filter' => fn () => Toggle::make('mesh_filter')
                ->live()
                ->label('Filtr wstępny'),

            'carbon_filter' => fn () => Toggle::make('carbon_filter')
                ->live()
                ->label('Filtr węglowy'),

            'rated_voltage' => fn () => TextInput::make('rated_voltage')
                ->numeric()
                ->label('Napięcie znamionowe'),

            'width' => fn () => TextInput::make('width')
                ->numeric()
                ->label('Szerokość'),

            'height' => fn () => TextInput::make('height')
                ->numeric()
                ->label('Wysokość'),

            'weight' => fn () => TextInput::make('weight')
                ->numeric()
                ->label('Waga'),

            'depth' => fn () => TextInput::make('depth')
                ->numeric()
                ->label('Głębokość'),

            'for_plant' => fn () => Toggle::make('for_plant')
                ->label('Do roślin'),

            'for_desk' => fn () => Toggle::make('for_desk')
                ->label('Na biurko'),

            'alergic' => fn () => Toggle::make('alergic')
                ->label('Dla alergików'),

            'astmatic' => fn () => Toggle::make('astmatic')
                ->label('Dla astmatyków'),

            'small' => fn () => Toggle::make('small')
                ->label('Mały rozmiar'),

            'for_kids' => fn () => Toggle::make('for_kids')
                ->label('Dla dzieci'),

            'big_area' => fn () => Toggle::make('big_area')
                ->label('Duża powierzchnia'),

            'colors' => fn () => TagsInput::make('colors')
                ->placeholder('Dodaj kolor')
                ->separator(',')
                ->label('Kolory'),

            'gallery' => fn () => FileUpload::make('gallery')
                ->label('Galeria zdjęć')
                ->directory('air-humidifiers')
                ->image(),

            'disks' => fn () => Toggle::make('disks')
                ->label('Dyski'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_humidifiers');

        return $schema
            ->components([
                Tabs::make('Formularz Nawilżacza Powietrza')
                    ->tabs(FormLayoutService::buildForm('air_humidifiers', static::getFieldDefinitions(), $customFieldSchema))
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = CustomFieldService::getTableColumns('air_humidifiers');

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->filters([])
            ->headerActions([
                ImportAction::make()
                    ->importer(AirHumidifierImporter::class),
                ExportActionService::createExportAllAction('air_humidifiers'),
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'air_humidifiers',
                            ],
                        ],
                    ])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('air_humidifiers'),
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
            'index' => ListAirHumidifiers::route('/'),
            'create' => CreateAirHumidifier::route('/create'),
            'edit' => EditAirHumidifier::route('/{record}/edit'),
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
