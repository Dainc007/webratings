<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DehumidifierType;
use App\Filament\Components\FormFieldSearch;
use App\Filament\Imports\DehumidifierImporter;
use App\Filament\Resources\DehumidifierResource\Pages\CreateDehumidifier;
use App\Filament\Resources\DehumidifierResource\Pages\EditDehumidifier;
use App\Filament\Resources\DehumidifierResource\Pages\ListDehumidifiers;
use App\Models\Dehumidifier;
use App\Services\CustomFieldService;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('dehumidifiers');

        return $schema
            ->components([
                FormFieldSearch::make(),
                Tabs::make('Formularz Osuszacza')
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

                                        Select::make('type')
                                            ->label('Typ')
                                            ->options(DehumidifierType::getOptions())
                                            ->searchable(),

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
                                    ])->columns(2),
                                    Section::make('Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Ocena możliwości'),

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
                                    ])->columns(2)
                                    ->collapsible(),

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
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Linki Ceneo')
                                    ->schema([
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
                                    ])
                                    ->columns(2)
                                    ->collapsible(),

                                Section::make('Link do recenzji')
                                    ->schema([
                                        Textarea::make('review_link')
                                            ->label('Link do recenzji')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Wydajność osuszania')
                            ->schema([
                                Section::make('Parametry osuszania')
                                    ->schema([
                                        TextInput::make('max_performance_dry')
                                            ->numeric()
                                            ->suffix('l/24h')
                                            ->label('Maksymalna wydajność osuszania'),

                                        TextInput::make('other_performance_dry')
                                            ->numeric()
                                            ->suffix('l/24h')
                                            ->label('Inna wydajność osuszania'),

                                        TextInput::make('max_performance_dry_condition')
                                            ->label('Warunki maksymalnej wydajności'),

                                        TextInput::make('other_performance_condition')
                                            ->label('Warunki innej wydajności'),

                                        TextInput::make('max_drying_area_manufacturer')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia osuszania (producent)'),

                                        TextInput::make('max_drying_area_ro')
                                            ->numeric()
                                            ->suffix('m²')
                                            ->label('Maks. powierzchnia osuszania (RO)'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Warunki pracy')
                            ->schema([
                                Section::make('Zakres temperatur')
                                    ->schema([
                                        TextInput::make('minimum_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Minimalna temperatura'),

                                        TextInput::make('maximum_temperature')
                                            ->numeric()
                                            ->suffix('°C')
                                            ->label('Maksymalna temperatura'),
                                    ])->columns(2),

                                Section::make('Zakres wilgotności')
                                    ->schema([
                                        TextInput::make('minimum_humidity')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Minimalna wilgotność'),

                                        TextInput::make('maximum_humidity')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Maksymalna wilgotność'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Zbiornik na wodę')
                            ->schema([
                                Section::make('Parametry zbiornika')
                                    ->schema([
                                        TextInput::make('water_tank_capacity')
                                            ->numeric()
                                            ->suffix('l')
                                            ->label('Pojemność zbiornika na wodę'),

                                        TextInput::make('minimum_fill_time')
                                            ->numeric()
                                            ->suffix('h')
                                            ->label('Minimalny czas napełniania'),

                                        TextInput::make('average_filling_time')
                                            ->numeric()
                                            ->suffix('h')
                                            ->label('Średni czas napełniania'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Higrostat i sterowanie')
                            ->schema([
                                Section::make('Higrostat')
                                    ->schema([
                                        Toggle::make('higrostat')
                                            ->label('Higrostat')
                                            ->live()
                                            ->columnSpanFull(),

                                        TextInput::make('min_value_for_hygrostat')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Minimalna wartość higrostatu')
                                            ->visible(fn (callable $get) => $get('higrostat')),

                                        TextInput::make('max_value_for_hygrostat')
                                            ->numeric()
                                            ->suffix('%')
                                            ->label('Maksymalna wartość higrostatu')
                                            ->visible(fn (callable $get) => $get('higrostat')),

                                        TextInput::make('increment_of_the_hygrostat')
                                            ->label('Skok higrostatu')
                                            ->visible(fn (callable $get) => $get('higrostat')),
                                    ])->columns(2),

                                Section::make('Wentylator')
                                    ->schema([
                                        TextInput::make('number_of_fan_speeds')
                                            ->numeric()
                                            ->label('Liczba prędkości wentylatora'),

                                        TextInput::make('max_air_flow')
                                            ->numeric()
                                            ->suffix('m³/h')
                                            ->label('Maksymalny przepływ powietrza'),
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

                                Section::make('Tryby pracy')
                                    ->schema([
                                        TagsInput::make('modes_of_operation')
                                            ->label('Tryby pracy')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Filtry i oczyszczanie')
                            ->schema([
                                Section::make('Filtry podstawowe')
                                    ->schema([
                                        Toggle::make('mesh_filter')
                                            ->label('Filtr wstępny'),
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

                                        Toggle::make('uv_light_generator')
                                            ->visible(fn (callable $get) => $get('uvc'))
                                            ->label('Generator światła UV'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Sterowanie i łączność')
                            ->schema([
                                Section::make('Sterowanie')
                                    ->schema([
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
                                        Select::make('productFunctions')
                                            ->relationship('productFunctions', 'name')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nazwa')
                                                    ->required(),
                                            ])
                                            ->label('Funkcje')
                                            ->multiple()
                                            ->columnSpanFull(),

                                        //                                        TagsInput::make('functions_and_equipment_dehumi')
                                        //                                            ->label('Funkcje i wyposażenie osuszacza')
                                        //                                            ->columnSpanFull(),
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
                                        TextInput::make('rated_power_consumption')
                                            ->numeric()
                                            ->suffix('W')
                                            ->label('Zużycie energii'),

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
                            ]),

                        Tab::make('Dodatkowe informacje')
                            ->schema([
                                Section::make('Galeria i dokumentacja')
                                    ->schema([
                                        // todo implement full file upload
                                        FileUpload::make('gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('dehumidifiers')
                                            ->image(),
                                        // todo
                                        FileUpload::make('manual_file')
                                            ->directory('instructions')
                                            ->label('Plik instrukcji'),
                                        //                                        TextInput::make('manual_file')
                                        //                                            ->label('Plik instrukcji'),
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
                            )
                            ->visible(fn () => count($customFieldSchema) > 0),
                    ]),
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
