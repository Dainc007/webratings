<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Components\FormFieldSearch;
use App\Filament\Imports\AirHumidifierImporter;
use App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\EditAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\ListAirHumidifiers;
use App\Models\AirHumidifier;
use App\Models\Brand;
use App\Services\CustomFieldService;
use App\Services\LabelService;
use App\Services\FormLayoutService;
use App\Services\ExportActionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\CheckboxList;
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

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_humidifiers');

        $defaultTabs = [
            Tab::make(LabelService::tab('air_humidifiers', 'Podstawowe informacje'))
                            ->schema([
                                LabelService::sectionMake('air_humidifiers', 'Podstawowe informacje')
                                    ->schema([
                                        TextInput::make('remote_id')
                                            ->label('ID zdalne'),
                                        Select::make('status')
                                            ->default('draft')
                                            ->selectablePlaceholder(false)
                                            ->options(Status::getOptions())
                                            ->label('Status'),
                                        TextInput::make('model')
                                            ->label('Model'),
                                        Select::make('brand_name')
                                            ->label('Marka')
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
                                            ->label('Cena'),
                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->label('Cena przed'),

                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                LabelService::sectionMake('air_humidifiers', 'Linki partnerskie')
                                    ->schema([
                                        TextInput::make('partner_link_url')
                                            ->url()
                                            ->label('Link partnerski'),

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

                                LabelService::sectionMake('air_humidifiers', 'Linki Ceneo')
                                    ->schema([
                                        TextInput::make('ceneo_url')
                                            ->url()
                                            ->label('Link Ceneo'),
                                    ])
                                    ->collapsible(),

                                LabelService::sectionMake('air_humidifiers', 'Link do recenzji')
                                    ->schema([
                                        TextInput::make('review_link')
                                            ->url()
                                            ->label('Link do recenzji'),
                                    ])
                                    ->collapsible(),

                                LabelService::sectionMake('air_humidifiers', 'Ranking')
                                    ->schema([
                                        TextInput::make('capability')
                                            ->label('Możliwości'),
                                        TextInput::make('ranking')
                                            ->label('Ranking'),
                                        TextInput::make('profitability')
                                            ->label('Opłacalność'),
                                        TextInput::make('popularity')
                                            ->numeric()
                                            ->label('Popularność'),
                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),
                                        Toggle::make('main_ranking')
                                            ->label('Ranking główny'),
                                    ])->columns(2)
                                    ->collapsible(),

                                LabelService::sectionMake('air_humidifiers', 'Galeria')
                                    ->schema([
                                        FileUpload::make('gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('air-humidifiers')
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

                                LabelService::sectionMake('air_humidifiers', 'Typy i kategorie')
                                    ->schema([
                                        Select::make('types')
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

                                        Select::make('type_of_device')
                                            ->label('Typ urządzenia')
                                            ->options([
                                                'ultradźwiękowy' => 'Ultradźwiękowy',
                                                'ewaporacyjny' => 'Ewaporacyjny',
                                                'parowy' => 'Parowy',
                                                'nawilżacz z oczyszczaczem' => 'Nawilżacz z oczyszczaczem',
                                            ])
                                            ->searchable(),
                                    ])
                                    ->collapsible(),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Wydajność'))
                            ->schema([
                                LabelService::sectionMake('air_humidifiers', 'Wydajność')
                                    ->schema([
                                        TextInput::make('max_performance')
                                            ->numeric()
                                            ->label('Maksymalna wydajność'),
                                        TextInput::make('max_area')
                                            ->numeric()
                                            ->label('Maksymalna powierzchnia'),
                                        TextInput::make('max_area_ro')
                                            ->numeric()
                                            ->label('Maksymalna powierzchnia RO'),
                                        TextInput::make('tested_efficiency')
                                            ->numeric()
                                            ->label('Wydajność testowana'),
                                    ])->columns(2),

                                LabelService::sectionMake('air_humidifiers', 'Głośność wentylatora')
                                    ->schema([
                                        Toggle::make('fan_volume')
                                            ->label('Głośność Wentylatora')
                                            ->live(),
                                        TextInput::make('min_fan_volume')
                                            ->visible(fn (callable $get) => $get('fan_volume'))
                                            ->hint('w decybelach (dB)')
                                            ->numeric()
                                            ->label('Min. głośność'),
                                        TextInput::make('max_fan_volume')
                                            ->hint('w decybelach (dB)')
                                            ->visible(fn (callable $get) => $get('fan_volume'))
                                            ->numeric()
                                            ->label('Max głośność'),
                                    ])->columns(2),

                                LabelService::sectionMake('air_humidifiers', 'Pobór mocy')
                                    ->schema([
                                        TextInput::make('min_rated_power_consumption')
                                            ->numeric()
                                            ->label('Minimalny pobór mocy'),
                                        TextInput::make('max_rated_power_consumption')
                                            ->numeric()
                                            ->label('Maksymalny pobór mocy'),
                                    ])->columns(2),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Zbiornik na wodę'))
                            ->schema([
                                LabelService::sectionMake('air_humidifiers', 'Zbiornik na wodę')
                                    ->schema([
                                        TextInput::make('water_tank_capacity')
                                            ->numeric()
                                            ->label('Pojemność zbiornika na wodę'),
                                        TextInput::make('water_tank_min_time')
                                            ->numeric()
                                            ->label('Minimalny czas pracy zbiornika'),
                                        Select::make('water_tank_fill_type')
                                            ->label('Typ napełniania zbiornika')
                                            ->options([
                                                'zdjecie_pokrywy' => 'Zdjęcie pokrywy',
                                                'zdjecie_pokrywy_okienko' => 'Zdjęcie pokrywy + okienko',
                                                'nalewanie_od_gory' => 'Nalewanie od góry',
                                            ]),
                                    ])->columns(2),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Sterowanie'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                Toggle::make('hygrostat')
                                                    ->live()
                                                    ->label('Higrostat'),
                                                TextInput::make('hygrostat_min')
                                                    ->visible(fn (callable $get) => $get('hygrostat'))
                                                    ->numeric()
                                                    ->label('Higrostat min'),
                                                TextInput::make('hygrostat_max')
                                                    ->visible(fn (callable $get) => $get('hygrostat'))
                                                    ->numeric()
                                                    ->label('Higrostat max'),
                                                Toggle::make('timer')
                                                    ->live()
                                                    ->label('Timer'),
                                                TextInput::make('timer_min')
                                                    ->visible(fn (callable $get) => $get('timer'))
                                                    ->numeric()
                                                    ->label('Timer min'),
                                                TextInput::make('timer_max')
                                                    ->visible(fn (callable $get) => $get('timer'))
                                                    ->numeric()
                                                    ->label('Timer max'),
                                            ]),
                                        Grid::make(1)
                                            ->schema([
                                                Toggle::make('auto_mode')
                                                    ->label('Tryb automatyczny'),
                                                Toggle::make('night_mode')
                                                    ->live()
                                                    ->label('Tryb nocny'),
                                                TextInput::make('night_mode_min')
                                                    ->visible(fn (callable $get) => $get('night_mode'))
                                                    ->numeric()
                                                    ->label('Tryb nocny min'),
                                                TextInput::make('night_mode_max')
                                                    ->visible(fn (callable $get) => $get('night_mode'))
                                                    ->numeric()
                                                    ->label('Tryb nocny max'),
                                                Toggle::make('child_lock')
                                                    ->label('Blokada rodzicielska'),
                                                Toggle::make('display')
                                                    ->label('Wyświetlacz'),
                                                Toggle::make('remote_control')
                                                    ->label('Pilot'),
                                            ]),
                                    ]),
                                // Old JSON-based implementation kept for reference
                                // TagsInput::make('productFunctions')
                                //     ->separator(',')
                                //     ->columnSpanFull()
                                //     ->label('Funkcje'),

                                Select::make('productFunctions')
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
                                LabelService::sectionMake('air_humidifiers', 'Funkcje smart')
                                    ->schema([
                                        Toggle::make('mobile_app')
                                            ->label('Aplikacja mobilna'),
                                        CheckboxList::make('mobile_features')
                                            ->label('Obsługiwany zakres Wi-Fi')
                                            ->options([
                                                'Wi-Fi 2,4 GHz' => 'Wi-Fi 2,4 GHz',
                                                'Wi-Fi 5 GHz' => 'Wi-Fi 5 GHz',
                                            ])
                                            ->columns(2),
                                    ])->columns(2),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Filtry'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        LabelService::sectionMake('air_humidifiers', 'Filtr ewaporacyjny')
                                            ->schema([
                                                Toggle::make('evaporative_filter')
                                                    ->live()
                                                    ->label('Filtr ewaporacyjny'),
                                                TextInput::make('evaporative_filter_life')
                                                    ->visible(fn (callable $get) => $get('evaporative_filter'))
                                                    ->numeric()
                                                    ->label('Żywotność filtra ewaporacyjnego'),
                                                TextInput::make('evaporative_filter_price')
                                                    ->visible(fn (callable $get) => $get('evaporative_filter'))
                                                    ->numeric()
                                                    ->label('Cena filtra ewaporacyjnego'),
                                            ]),
                                        LabelService::sectionMake('air_humidifiers', 'Srebrna jonizacja')
                                            ->schema([
                                                Toggle::make('silver_ion')
                                                    ->live()
                                                    ->label('Srebrna jonizacja'),
                                                TextInput::make('silver_ion_life')
                                                    ->visible(fn (callable $get) => $get('silver_ion'))
                                                    ->numeric()
                                                    ->label('Żywotność srebrnej jonizacji'),
                                                TextInput::make('silver_ion_price')
                                                    ->visible(fn (callable $get) => $get('silver_ion'))
                                                    ->numeric()
                                                    ->label('Cena srebrnej jonizacji'),
                                            ]),
                                        LabelService::sectionMake('air_humidifiers', 'Filtr ceramiczny')
                                            ->schema([
                                                Toggle::make('ceramic_filter')
                                                    ->live()
                                                    ->label('Filtr ceramiczny'),
                                                TextInput::make('ceramic_filter_life')
                                                    ->visible(fn (callable $get) => $get('ceramic_filter'))
                                                    ->numeric()
                                                    ->label('Żywotność filtra ceramicznego'),
                                                TextInput::make('ceramic_filter_price')
                                                    ->visible(fn (callable $get) => $get('ceramic_filter'))
                                                    ->numeric()
                                                    ->label('Cena filtra ceramicznego'),
                                            ]),
                                        LabelService::sectionMake('air_humidifiers', 'Filtr węglowy')
                                            ->schema([
                                                Toggle::make('carbon_filter')
                                                    ->live()
                                                    ->label('Filtr węglowy'),
                                                TextInput::make('carbon_filter_price')
                                                    ->visible(fn (callable $get) => $get('carbon_filter'))
                                                    ->numeric()
                                                    ->prefix('PLN')
                                                    ->label('Koszt filtra węglowego'),
                                                TextInput::make('carbon_filter_service_life')
                                                    ->visible(fn (callable $get) => $get('carbon_filter'))
                                                    ->numeric()
                                                    ->suffix('miesięcy')
                                                    ->label('Żywotność filtra węglowego'),
                                            ]),
                                        LabelService::sectionMake('air_humidifiers', 'Inne filtry')
                                            ->schema([
                                                Toggle::make('uv_lamp')
                                                    ->live()
                                                    ->label('Lampa UV'),
                                                Toggle::make('ionization')
                                                    ->live()
                                                    ->label('Jonizacja'),
                                                TextInput::make('hepa_filter_class')
                                                    ->label('Klasa filtra HEPA'),
                                                Toggle::make('mesh_filter')
                                                    ->live()
                                                    ->label('Filtr wstępny'),
                                            ]),
                                    ]),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Wymiary'))
                            ->schema([
                                LabelService::sectionMake('air_humidifiers', 'Wymiary')
                                    ->schema([
                                        TextInput::make('rated_voltage')
                                            ->numeric()
                                            ->label('Napięcie znamionowe'),
                                        TextInput::make('width')
                                            ->numeric()
                                            ->label('Szerokość'),
                                        TextInput::make('height')
                                            ->numeric()
                                            ->label('Wysokość'),
                                        TextInput::make('weight')
                                            ->numeric()
                                            ->label('Waga'),
                                        TextInput::make('depth')
                                            ->numeric()
                                            ->label('Głębokość'),
                                    ])->columns(2),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Kategorie'))
                            ->schema([
                                LabelService::sectionMake('air_humidifiers', 'Kategorie')
                                    ->schema([
                                        Toggle::make('for_plant')
                                            ->label('Do roślin'),
                                        Toggle::make('for_desk')
                                            ->label('Na biurko'),
                                        Toggle::make('alergic')
                                            ->label('Dla alergików'),
                                        Toggle::make('astmatic')
                                            ->label('Dla astmatyków'),
                                        Toggle::make('small')
                                            ->label('Mały rozmiar'),
                                        Toggle::make('for_kids')
                                            ->label('Dla dzieci'),
                                        Toggle::make('big_area')
                                            ->label('Duża powierzchnia'),
                                    ])->columns(2),
                            ]),
                        Tab::make(LabelService::tab('air_humidifiers', 'Dodatkowe'))
                            ->schema([
                                LabelService::sectionMake('air_humidifiers', 'Dodatkowe')
                                    ->schema([
                                        TagsInput::make('colors')
                                            ->placeholder('Dodaj kolor')
                                            ->separator(',')
                                            ->label('Kolory'),

                                        Toggle::make('disks')
                                            ->label('Dyski'),
                                    ])->columns(2),
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
                Tabs::make('Formularz Nawilżacza Powietrza')
                    ->tabs(FormLayoutService::applyLayout('air_humidifiers', $defaultTabs))
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
                        'filters' => [
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
