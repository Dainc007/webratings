<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use App\Services\CustomFieldService;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Actions\ImportAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AirConditionerResource\Pages\ListAirConditioners;
use App\Filament\Resources\AirConditionerResource\Pages\CreateAirConditioner;
use App\Filament\Resources\AirConditionerResource\Pages\EditAirConditioner;
use App\Filament\Imports\AirConditionerImporter;
use App\Filament\Resources\AirConditionerResource\Pages;
use App\Models\AirConditioner;
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

final class AirConditionerResource extends Resource
{
    protected static ?string $model = AirConditioner::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Klimatyzatory';

    protected static ?string $pluralLabel = 'Klimatyzatory';

    protected static ?string $label = 'Klimatyzator';

    protected static string | \UnitEnum | null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'model';

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('air_conditioners');

        return $schema
            ->components([
                Tabs::make('Formularz Klimatyzatora')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Podstawowe informacje')
                            ->schema([
                                Section::make('Podstawowe informacje')
                                    ->schema([
                                        Select::make('status')
                                            ->selectablePlaceholder(false)
                                            ->options([
                                                'draft' => 'Szkic',
                                                'published' => 'Opublikowany',
                                                'archived' => 'Zarchiwizowany',
                                            ])
                                            ->required()
                                            ->label('Status'),

                                        TextInput::make('model')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Model'),

                                        TextInput::make('brand_name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Marka'),

                                        TextInput::make('type'),

                                        TextInput::make('price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->label('Cena'),

                                        TextInput::make('price_before')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->label('Cena przed'),

                                        TextInput::make('image')
                                            ->disabled(),

                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),

                                        Toggle::make('is_promo')
                                            ->label('Promocja'),
                                    ])->columns(2),

                                Section::make('Linki partnerskie')
                                    ->schema([
                                        TextInput::make('partner_name')
                                            ->label('Nazwa partnera'),

                                        Textarea::make('partner_link_url')
                                            ->label('Link partnerski')
                                            ->columnSpanFull(),

                                        Select::make('partner_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Atrybuty rel partnera'),

                                        TextInput::make('partner_link_title')
                                            ->label('Tytuł linku partnera'),

                                        Textarea::make('ceneo_url')
                                            ->label('Link Ceneo')
                                            ->columnSpanFull(),

                                        Select::make('ceneo_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ])
                                            ->label('Atrybuty rel Ceneo'),

                                        TextInput::make('ceneo_link_title')
                                            ->label('Tytuł linku Ceneo'),

                                        Textarea::make('review_link')
                                            ->label('Link do recenzji')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),
                            ]),

                        Tab::make('Wydajność chłodzenia')
                            ->schema([
                                Section::make('Parametry chłodzenia')
                                    ->schema([
                                        TextInput::make('maximum_cooling_power')
                                            ->numeric()
                                            ->suffix('BTU/h')
                                            ->label('Maksymalna moc chłodzenia'),

                                        TextInput::make('max_cooling_area_manufacturer')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia chłodzenia (producent)'),

                                        TextInput::make('max_cooling_area_ro')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia chłodzenia (RO)'),

                                        TextInput::make('max_cooling_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Maksymalna temperatura chłodzenia'),

                                        TextInput::make('min_cooling_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Minimalna temperatura chłodzenia'),

                                        TextInput::make('cooling_energy_class')
                                            ->label('Klasa energetyczna chłodzenia'),

                                        TextInput::make('eer')
                                            ->numeric()
                                            ->label('EER (Efektywność energetyczna)'),

                                        TextInput::make('rated_power_cooling_consumption')
                                            ->numeric()
                                            ->suffix('W')
                                            ->label('Zużycie energii przy chłodzeniu'),

                                        Toggle::make('mode_cooling')
                                            ->label('Tryb chłodzenia'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Wydajność grzania')
                            ->schema([
                                Section::make('Parametry grzania')
                                    ->schema([
                                        TextInput::make('maximum_heating_power')
                                            ->numeric()
                                            ->suffix('BTU/h')
                                            ->label('Maksymalna moc grzania'),

                                        TextInput::make('max_heating_area_manufacturer')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia grzania (producent)'),

                                        TextInput::make('max_heating_area_ro')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia grzania (RO)'),

                                        TextInput::make('max_heating_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Maksymalna temperatura grzania'),

                                        TextInput::make('min_heating_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Minimalna temperatura grzania'),

                                        TextInput::make('heating_energy_class')
                                            ->label('Klasa energetyczna grzania'),

                                        TextInput::make('cop')
                                            ->numeric()
                                            ->label('COP (Współczynnik wydajności)'),

                                        TextInput::make('rated_power_heating_consumption')
                                            ->numeric()
                                            ->suffix('W')
                                            ->label('Zużycie energii przy grzaniu'),

                                        Toggle::make('mode_heating')
                                            ->label('Tryb grzania'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Tryby pracy i funkcje')
                            ->schema([
                                Section::make('Tryby pracy')
                                    ->schema([
                                        Toggle::make('mode_dry')
                                            ->label('Tryb osuszania'),

                                        TextInput::make('max_performance_dry')
                                            ->numeric()
                                            ->suffix('l/24h')
                                            ->visible(fn (callable $get) => $get('mode_dry'))
                                            ->label('Maksymalna wydajność osuszania'),

                                        TextInput::make('max_performance_dry_condition')
                                            ->visible(fn (callable $get) => $get('mode_dry'))
                                            ->label('Warunki maksymalnej wydajności osuszania'),

                                        Toggle::make('mode_fan')
                                            ->label('Tryb wentylatora'),

                                        Toggle::make('mode_purify')
                                            ->label('Tryb oczyszczania'),
                                    ])->columns(2),

                                Section::make('Parametry powietrza')
                                    ->schema([
                                        TextInput::make('max_air_flow')
                                            ->numeric()
                                            ->suffix('m³/h')
                                            ->label('Maksymalny przepływ powietrza'),

                                        TextInput::make('number_of_fan_speeds')
                                            ->numeric()
                                            ->label('Liczba prędkości wentylatora'),

                                        TextInput::make('swing')
                                            ->label('Swing (kierowanie powietrzem)'),

                                        TextInput::make('temperature_range')
                                            ->label('Zakres temperatur'),
                                    ])->columns(2),

                                Section::make('Hałas')
                                    ->schema([
                                        TextInput::make('max_loudness')
                                            ->numeric()
                                            ->suffix('dB')
                                            ->label('Maksymalny poziom hałasu'),

                                        TextInput::make('min_loudness')
                                            ->numeric()
                                            ->suffix('dB')
                                            ->label('Minimalny poziom hałasu'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Filtry i oczyszczanie')
                            ->schema([
                                Section::make('Filtry podstawowe')
                                    ->schema([
                                        Toggle::make('mesh_filter')
                                            ->label('Filtr siatkowy'),
                                    ])->columns(1),

                                Section::make('Filtr HEPA')
                                    ->schema([
                                        Toggle::make('hepa_filter')
                                            ->live()
                                            ->label('Filtr HEPA'),

                                        TextInput::make('hepa_filter_price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->visible(fn (callable $get) => $get('hepa_filter'))
                                            ->label('Cena filtra HEPA'),

                                        TextInput::make('hepa_service_life')
                                            ->numeric()
                                            ->suffix('miesięcy')
                                            ->visible(fn (callable $get) => $get('hepa_filter'))
                                            ->label('Żywotność filtra HEPA'),
                                    ])->columns(2)->collapsible(),

                                Section::make('Filtr węglowy')
                                    ->schema([
                                        Toggle::make('carbon_filter')
                                            ->live()
                                            ->label('Filtr węglowy'),

                                        TextInput::make('carbon_filter_price')
                                            ->numeric()
                                            ->prefix('PLN')
                                            ->visible(fn (callable $get) => $get('carbon_filter'))
                                            ->label('Cena filtra węglowego'),

                                        TextInput::make('carbon_service_life')
                                            ->numeric()
                                            ->suffix('miesięcy')
                                            ->visible(fn (callable $get) => $get('carbon_filter'))
                                            ->label('Żywotność filtra węglowego'),
                                    ])->columns(2)->collapsible(),

                                Section::make('Dodatkowe technologie')
                                    ->schema([
                                        Toggle::make('ionization')
                                            ->label('Jonizacja'),

                                        Toggle::make('uvc')
                                            ->live()
                                            ->label('Lampa UV-C'),

                                        Textarea::make('uv_light_generator')
                                            ->visible(fn (callable $get) => $get('uvc'))
                                            ->label('Generator światła UV')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Sterowanie i łączność')
                            ->schema([
                                Section::make('Sterowanie')
                                    ->schema([
                                        Toggle::make('remote_control')
                                            ->label('Pilot zdalnego sterowania'),

                                        Toggle::make('mobile_app')
                                            ->live()
                                            ->label('Aplikacja mobilna'),

                                        TagsInput::make('mobile_features')
                                            ->visible(fn (callable $get) => $get('mobile_app'))
                                            ->label('Funkcje aplikacji mobilnej')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Funkcje i wyposażenie')
                                    ->schema([
                                        TagsInput::make('functions')
                                            ->label('Funkcje')
                                            ->columnSpanFull(),

                                        TagsInput::make('functions_and_equipment_condi')
                                            ->label('Funkcje i wyposażenie klimatyzatora')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Specyfikacja techniczna')
                            ->schema([
                                Section::make('Chłodziwo')
                                    ->schema([
                                        TextInput::make('refrigerant_kind')
                                            ->label('Rodzaj chłodziwa'),

                                        TextInput::make('refrigerant_amount')
                                            ->numeric()
                                            ->suffix('kg')
                                            ->label('Ilość chłodziwa'),

                                        TextInput::make('needs_to_be_completed')
                                            ->label('Wymaga uzupełnienia'),
                                    ])->columns(2),

                                Section::make('Parametry elektryczne')
                                    ->schema([
                                        TextInput::make('rated_voltage')
                                            ->numeric()
                                            ->suffix('V')
                                            ->label('Napięcie znamionowe'),
                                    ])->columns(2),

                                Section::make('Wymiary i waga')
                                    ->schema([
                                        TextInput::make('width')
                                            ->numeric()
                                            ->suffix('cm')
                                            ->label('Szerokość'),

                                        TextInput::make('height')
                                            ->numeric()
                                            ->suffix('cm')
                                            ->label('Wysokość'),

                                        TextInput::make('depth')
                                            ->numeric()
                                            ->suffix('cm')
                                            ->label('Głębokość'),

                                        TextInput::make('weight')
                                            ->numeric()
                                            ->suffix('kg')
                                            ->label('Waga'),
                                    ])->columns(2),

                                Section::make('Instalacja')
                                    ->schema([
                                        Toggle::make('discharge_pipe')
                                            ->live()
                                            ->label('Rura odprowadzająca'),

                                        TextInput::make('discharge_pipe_length')
                                            ->numeric()
                                            ->suffix('m')
                                            ->visible(fn (callable $get) => $get('discharge_pipe'))
                                            ->label('Długość rury odprowadzającej'),

                                        TextInput::make('discharge_pipe_diameter')
                                            ->numeric()
                                            ->suffix('mm')
                                            ->visible(fn (callable $get) => $get('discharge_pipe'))
                                            ->label('Średnica rury odprowadzającej'),

                                        Toggle::make('drain_hose')
                                            ->label('Wąż odprowadzający'),

                                        TextInput::make('sealing')
                                            ->label('Uszczelnienie'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Dodatkowe informacje')
                            ->schema([
                                Section::make('Wygląd')
                                    ->schema([
                                        TagsInput::make('colors')
                                            ->label('Dostępne kolory')
                                            ->columnSpanFull(),

                                        TextInput::make('usage')
                                            ->label('Zastosowanie'),

                                        FileUpload::make('gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('air-conditioners')
                                            ->image(),
                                    ]),

                                Section::make('Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability_points')
                                            ->numeric()
                                            ->label('Punkty za możliwości'),

                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Ocena możliwości'),

                                        TextInput::make('profitability_points')
                                            ->numeric()
                                            ->label('Punkty za opłacalność'),

                                        TextInput::make('profitability')
                                            ->numeric()
                                            ->label('Ocena opłacalności'),

                                        TextInput::make('ranking')
                                            ->numeric()
                                            ->label('Pozycja w rankingu'),

                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),

                                        Toggle::make('main_ranking')
                                            ->label('Główny ranking'),

                                        TextInput::make('small')
                                            ->label('Mały'),
                                    ])->columns(2),

                                Section::make('Dokumentacja')
                                    ->schema([
                                        FileUpload::make('manual')
                                            ->directory('instructions')
                                            ->label('Instrukcja obsługi'),
                                        TextInput::make('manual')
                                            ->disabled()
                                            ->label('Instrukcja obsługi'),
                                    ]),

                                Section::make('Dane systemowe')
                                    ->schema([
                                        TextInput::make('remote_id')
                                            ->numeric()
                                            ->label('ID zewnętrzne'),

                                        TextInput::make('sort')
                                            ->numeric()
                                            ->label('Kolejność sortowania'),

                                        TextInput::make('user_created')
                                            ->label('Utworzony przez'),

                                        DateTimePicker::make('date_created')
                                            ->label('Data utworzenia'),

                                        TextInput::make('user_updated')
                                            ->label('Zaktualizowany przez'),

                                        DateTimePicker::make('date_updated')
                                            ->label('Data aktualizacji'),
                                    ])->columns(2)->collapsible(),
                            ]),

                        Tab::make('custom_fields')
                            ->schema(
                                $customFieldSchema
                            ),
                    ]),
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
                    ->url(fn () => route('filament.admin.resources.table-column-preferences.index', [
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

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }
}
