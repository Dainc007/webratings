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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
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

        $defaultTabs = [
                        Tab::make(LabelService::tab('dehumidifiers', 'Podstawowe informacje'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Podstawowe informacje')
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

                                        Select::make('brand_name')
                                            ->label('Marka')
                                            ->required()
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
                                        Textarea::make('discount_info')
                                            ->label('Informacje o zniżce')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                                    LabelService::sectionMake('dehumidifiers', 'Oceny i ranking')
                                    ->schema([
                                        TextInput::make('capability')
                                            ->numeric()
                                            ->label('Ocena możliwości'),

                                        TextInput::make('profitability')
                                            ->numeric()
                                            ->label('Ocena opłacalności'),

                                        TextInput::make('popularity')
                                            ->numeric()
                                            ->label('Popularność'),

                                        TextInput::make('ranking')
                                            ->numeric()
                                            ->label('Pozycja w rankingu'),

                                        Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),

                                        Toggle::make('main_ranking')
                                            ->label('Główny ranking'),
                                    ])->columns(2)
                                    ->collapsible(),

                                LabelService::sectionMake('dehumidifiers', 'Linki partnerskie')
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

                                LabelService::sectionMake('dehumidifiers', 'Linki Ceneo')
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

                                LabelService::sectionMake('dehumidifiers', 'Link do recenzji')
                                    ->schema([
                                        Textarea::make('review_link')
                                            ->label('Link do recenzji')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                LabelService::sectionMake('dehumidifiers', 'Galeria')
                                    ->schema([
                                        FileUpload::make('local_gallery')
                                            ->label('Galeria zdjęć')
                                            ->directory('dehumidifiers')
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

                        Tab::make(LabelService::tab('dehumidifiers', 'Wydajność osuszania'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Parametry osuszania')
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

                        Tab::make(LabelService::tab('dehumidifiers', 'Warunki pracy'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Zakres temperatur')
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

                                LabelService::sectionMake('dehumidifiers', 'Zakres wilgotności')
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

                        Tab::make(LabelService::tab('dehumidifiers', 'Zbiornik na wodę'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Parametry zbiornika')
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

                        Tab::make(LabelService::tab('dehumidifiers', 'Higrostat i sterowanie'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Higrostat')
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

                                LabelService::sectionMake('dehumidifiers', 'Wentylator')
                                    ->schema([
                                        TextInput::make('number_of_fan_speeds')
                                            ->numeric()
                                            ->label('Liczba prędkości wentylatora'),

                                        TextInput::make('max_air_flow')
                                            ->numeric()
                                            ->suffix('m³/h')
                                            ->label('Maksymalny przepływ powietrza'),
                                    ])->columns(2),

                                LabelService::sectionMake('dehumidifiers', 'Hałas')
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

                                LabelService::sectionMake('dehumidifiers', 'Tryby pracy')
                                    ->schema([
                                        Select::make('modes_of_operation')
                                            ->label('Tryby pracy')
                                            ->multiple()
                                            ->searchable()
                                            ->options([
                                                'piwnica' => 'Basement (piwnica)',
                                                'sypialnia' => 'Bedroom (sypialnia)',
                                                'praca_ciagla' => 'Continous (praca ciągła)',
                                                'osuszanie_prania' => 'Osuszanie prania',
                                                'swing' => 'Swing',
                                                'tryb_automatyczny' => 'Tryb automatyczny',
                                            ])
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nazwa trybu')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(fn (array $data): string => $data['name'])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(LabelService::tab('dehumidifiers', 'Filtry i oczyszczanie'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Filtry podstawowe')
                                    ->schema([
                                        Toggle::make('mesh_filter')
                                            ->label('Filtr wstępny'),
                                    ])->columns(1),

                                LabelService::sectionMake('dehumidifiers', 'Filtr HEPA')
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

                                LabelService::sectionMake('dehumidifiers', 'Filtr węglowy')
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

                                LabelService::sectionMake('dehumidifiers', 'Dodatkowe technologie')
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

                        Tab::make(LabelService::tab('dehumidifiers', 'Sterowanie i łączność'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Sterowanie')
                                    ->schema([
                                        Toggle::make('mobile_app')
                                            ->live()
                                            ->label('Aplikacja mobilna'),

                                        TagsInput::make('mobile_features')
                                            ->visible(fn (callable $get) => $get('mobile_app'))
                                            ->label('Funkcje aplikacji mobilnej')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                LabelService::sectionMake('dehumidifiers', 'Funkcje i wyposażenie')
                                    ->schema([
                                        Select::make('productFunctions')
                                            ->relationship('productFunctions', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nazwa')
                                                    ->required(),
                                            ])
                                            ->label('Funkcje')
                                            ->columnSpanFull(),

                                        //                                        TagsInput::make('functions_and_equipment_dehumi')
                                        //                                            ->label('Funkcje i wyposażenie osuszacza')
                                        //                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(LabelService::tab('dehumidifiers', 'Specyfikacja techniczna'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Chłodziwo')
                                    ->schema([
                                        Select::make('refrigerant_kind')
                                            ->label('Rodzaj chłodziwa')
                                            ->options([
                                                'R290' => 'R290',
                                                'R410a' => 'R410a',
                                                'R32' => 'R32',
                                            ]),

                                        TextInput::make('refrigerant_amount')
                                            ->numeric()
                                            ->suffix('g')
                                            ->label('Ilość chłodziwa'),

                                        Toggle::make('needs_to_be_completed')
                                            ->label('Wymaga uzupełnienia'),
                                    ])->columns(2),

                                LabelService::sectionMake('dehumidifiers', 'Parametry elektryczne')
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

                                LabelService::sectionMake('dehumidifiers', 'Wymiary i waga')
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

                        Tab::make(LabelService::tab('dehumidifiers', 'Dodatkowe informacje'))
                            ->schema([
                                LabelService::sectionMake('dehumidifiers', 'Dokumentacja')
                                    ->schema([
                                        FileUpload::make('manual_file')
                                            ->directory('instructions')
                                            ->label('Plik instrukcji'),
                                    ]),

                                LabelService::sectionMake('dehumidifiers', 'Dane systemowe')
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
        ];

        return $schema
            ->components([
                FormFieldSearch::make(),
                Tabs::make('Formularz Osuszacza')
                    ->columnSpanFull()
                    ->tabs(FormLayoutService::applyLayout('dehumidifiers', $defaultTabs))
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
                        'filters' => [
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
