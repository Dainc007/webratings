<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use App\Services\CustomFieldService;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Actions\ImportAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AirHumidifierResource\Pages\ListAirHumidifiers;
use App\Filament\Resources\AirHumidifierResource\Pages\CreateAirHumidifier;
use App\Filament\Resources\AirHumidifierResource\Pages\EditAirHumidifier;
use App\Filament\Imports\AirHumidifierImporter;
use App\Filament\Resources\AirHumidifierResource\Pages;
use App\Models\AirHumidifier;
use Filament\Forms;
use Filament\Forms\Components\TagsInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\ExportActionService;
use Filament\Forms\Components\Textarea;

final class AirHumidifierResource extends Resource
{
    protected static ?string $model = AirHumidifier::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Nawilżacze Powietrza';

    protected static ?string $pluralLabel = 'Nawilżacze Powietrza';

    protected static ?string $label = 'Nawilżacze Powietrza';

    protected static string | \UnitEnum | null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_humidifiers');

        return $schema
            ->components([
                Tabs::make('Formularz Nawilżacza Powietrza')
                    ->tabs([
                        Tab::make('Podstawowe informacje')
                            ->schema([
                                Section::make('Podstawowe informacje')
                                    ->schema([
                                        TextInput::make('remote_id')
                                            ->label('ID zdalne'),
                                        TextInput::make('status')
                                            ->label('Status'),
                                        TextInput::make('model')
                                            ->label('Model'),
                                        TextInput::make('brand_name')
                                            ->label('Marka'),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->label('Cena'),
                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->label('Cena przed'),
                                    ])->columns(2),

                                Section::make('Informacje o zniżce')
                                    ->schema([
                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Section::make('Linki partnerskie')
                                    ->schema([
                                        TextInput::make('partner_link_url')
                                            ->url()
                                            ->label('Link partnerski'),
                                    ])
                                    ->collapsible(),

                                Section::make('Linki Ceneo')
                                    ->schema([
                                        TextInput::make('ceneo_url')
                                            ->url()
                                            ->label('Link Ceneo'),
                                    ])
                                    ->collapsible(),

                                Section::make('Link do recenzji')
                                    ->schema([
                                        TextInput::make('review_link')
                                            ->url()
                                            ->label('Link do recenzji'),
                                    ])
                                    ->collapsible(),
                            ]),
                        Tab::make('Wydajność')
                            ->schema([
                                Section::make('Wydajność')
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
                                        TextInput::make('humidification_area')
                                            ->numeric()
                                            ->label('Powierzchnia nawilżania'),
                                    ])->columns(2),
                            ]),
                        Tab::make('Zbiornik na wodę')
                            ->schema([
                                Section::make('Zbiornik na wodę')
                                    ->schema([
                                        TextInput::make('water_tank_capacity')
                                            ->numeric()
                                            ->label('Pojemność zbiornika na wodę'),
                                        TextInput::make('water_tank_min_time')
                                            ->numeric()
                                            ->label('Minimalny czas pracy zbiornika'),
                                        TextInput::make('water_tank_fill_type')
                                            ->label('Typ napełniania zbiornika'),
                                    ])->columns(2),
                            ]),
                        Tab::make('Sterowanie')
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
                                                    ->live()
                                                    ->label('Tryb automatyczny'),
                                                TextInput::make('auto_mode_min')
                                                    ->visible(fn (callable $get) => $get('auto_mode'))
                                                    ->numeric()
                                                    ->label('Tryb auto min'),
                                                TextInput::make('auto_mode_max')
                                                    ->visible(fn (callable $get) => $get('auto_mode'))
                                                    ->numeric()
                                                    ->label('Tryb auto max'),
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
                                                    ->live()
                                                    ->label('Blokada rodzicielska'),
                                                TextInput::make('child_lock_min')
                                                    ->visible(fn (callable $get) => $get('child_lock'))
                                                    ->numeric()
                                                    ->label('Blokada rodzicielska min'),
                                                TextInput::make('child_lock_max')
                                                    ->visible(fn (callable $get) => $get('child_lock'))
                                                    ->numeric()
                                                    ->label('Blokada rodzicielska max'),
                                                Toggle::make('display')
                                                    ->live()
                                                    ->label('Wyświetlacz'),
                                                TextInput::make('display_min')
                                                    ->visible(fn (callable $get) => $get('display'))
                                                    ->numeric()
                                                    ->label('Wyświetlacz min'),
                                                TextInput::make('display_max')
                                                    ->visible(fn (callable $get) => $get('display'))
                                                    ->numeric()
                                                    ->label('Wyświetlacz max'),
                                                Toggle::make('remote_control')
                                                    ->live()
                                                    ->label('Pilot'),
                                                TextInput::make('remote_control_min')
                                                    ->visible(fn (callable $get) => $get('remote_control'))
                                                    ->numeric()
                                                    ->label('Pilot min'),
                                                TextInput::make('remote_control_max')
                                                    ->visible(fn (callable $get) => $get('remote_control'))
                                                    ->numeric()
                                                    ->label('Pilot max'),
                                            ]),
                                    ]),
                                TagsInput::make('functions')
                                    ->separator(',')
                                    ->columnSpanFull()
                                    ->label('Funkcje'),
                                Section::make('Funkcje smart')
                                    ->schema([
                                        Toggle::make('mobile_app')
                                            ->label('Aplikacja mobilna'),
                                        TagsInput::make('mobile_features')
                                            ->placeholder('Dodaj funkcję')
                                            ->separator(',')
                                            ->label('Funkcje aplikacji'),
                                    ])->columns(2),
                            ]),
                        Tab::make('Filtry')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Filtr ewaporacyjny')
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
                                        Section::make('Srebrna jonizacja')
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
                                        Section::make('Filtr ceramiczny')
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
                                        Section::make('Inne filtry')
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
                                                    ->label('Filtr siatkowy'),
                                                Toggle::make('carbon_filter')
                                                    ->live()
                                                    ->label('Filtr węglowy'),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Zasilanie i wymiary')
                            ->schema([
                                Section::make('Zasilanie i wymiary')
                                    ->schema([
                                        TextInput::make('min_rated_power_consumption')
                                            ->numeric()
                                            ->label('Minimalny pobór mocy'),
                                        TextInput::make('max_rated_power_consumption')
                                            ->numeric()
                                            ->label('Maksymalny pobór mocy'),
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
                        Tab::make('Kategorie')
                            ->schema([
                                Section::make('Kategorie')
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
                        Tab::make('Ranking')
                            ->schema([
                                Section::make('Ranking')
                                    ->schema([
                                        TextInput::make('capability_points')
                                            ->numeric()
                                            ->label('Punkty za możliwości'),
                                        TextInput::make('capability')
                                            ->label('Możliwości'),
                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->label('Punkty za opłacalność'),
                                        TextInput::make('ranking')
                                            ->label('Ranking'),
                                        TextInput::make('profitability')
                                            ->label('Opłacalność'),
                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),
                                        Toggle::make('main_ranking')
                                            ->label('Ranking główny'),
                                    ])->columns(2),
                            ]),
                        Tab::make('Dodatkowe')
                            ->schema([
                                Section::make('Dodatkowe')
                                    ->schema([
                                        TagsInput::make('colors')
                                            ->placeholder('Dodaj kolor')
                                            ->separator(',')
                                            ->label('Kolory'),
                                        TextInput::make('type_of_device')
                                            ->label('Typ urządzenia'),

                                        //todo
                                        FileUpload::make('gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('air-humidifiers')
                                            ->image(),
//                                        TagsInput::make('gallery')
//                                            ->placeholder('Dodaj zdjęcie')
//                                            ->separator(',')
//                                            ->label('Galeria'),
                                        TextInput::make('Filter_cots_humi')
                                            ->label('Koszty filtrów'),
                                        Toggle::make('disks')
                                            ->label('Dyski'),
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
                    ->url(fn () => route('filament.admin.resources.table-column-preferences.index', [
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

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
