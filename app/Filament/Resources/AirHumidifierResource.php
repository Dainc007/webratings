<?php

namespace App\Filament\Resources;

use App\Filament\Imports\AirHumidifierImporter;
use App\Filament\Resources\AirHumidifierResource\Pages;
use App\Filament\Resources\AirHumidifierResource\RelationManagers;
use App\Models\AirHumidifier;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TagsInput;

class AirHumidifierResource extends Resource
{
    protected static ?string $model = AirHumidifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Nawilżacze Powietrza';

    protected static ?string $pluralLabel = 'Nawilżacze Powietrza';

    protected static ?string $label = 'Nawilżacze Powietrza';

    protected static ?string $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'model';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Formularz Nawilżacza Powietrza')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Podstawowe informacje')
                            ->schema([
                                Forms\Components\Section::make('Podstawowe informacje')
                                    ->schema([
                                        Forms\Components\TextInput::make('remote_id')
                                            ->label('ID zdalne'),
                                        Forms\Components\TextInput::make('status')
                                            ->label('Status'),
                                        Forms\Components\TextInput::make('model')
                                            ->label('Model'),
                                        Forms\Components\TextInput::make('brand_name')
                                            ->label('Marka'),
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->label('Cena'),
                                        Forms\Components\TextInput::make('partner_link_url')
                                            ->url()
                                            ->label('Link partnerski'),
                                        Forms\Components\TextInput::make('ceneo_url')
                                            ->url()
                                            ->label('Link Ceneo'),
                                        Forms\Components\TextInput::make('review_link')
                                            ->url()
                                            ->label('Link do recenzji'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Wydajność')
                            ->schema([
                                Forms\Components\Section::make('Wydajność')
                                    ->schema([
                                        Forms\Components\TextInput::make('max_performance')
                                            ->numeric()
                                            ->label('Maksymalna wydajność'),
                                        Forms\Components\TextInput::make('max_area')
                                            ->numeric()
                                            ->label('Maksymalna powierzchnia'),
                                        Forms\Components\TextInput::make('max_area_ro')
                                            ->numeric()
                                            ->label('Maksymalna powierzchnia RO'),
                                        Forms\Components\TextInput::make('humidification_efficiency')
                                            ->numeric()
                                            ->label('Wydajność nawilżania'),
                                        Forms\Components\TextInput::make('tested_efficiency')
                                            ->numeric()
                                            ->label('Wydajność testowana'),
                                        Forms\Components\TextInput::make('humidification_area')
                                            ->numeric()
                                            ->label('Powierzchnia nawilżania'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Zbiornik na wodę')
                            ->schema([
                                Forms\Components\Section::make('Zbiornik na wodę')
                                    ->schema([
                                        Forms\Components\TextInput::make('water_tank_capacity')
                                            ->numeric()
                                            ->label('Pojemność zbiornika na wodę'),
                                        Forms\Components\TextInput::make('water_tank_min_time')
                                            ->numeric()
                                            ->label('Minimalny czas pracy zbiornika'),
                                        Forms\Components\TextInput::make('water_tank_fill_type')
                                            ->label('Typ napełniania zbiornika'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Sterowanie')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Toggle::make('hygrostat')
                                                    ->live()
                                                    ->label('Higrostat'),
                                                Forms\Components\TextInput::make('hygrostat_min')
                                                    ->visible(fn(callable $get) => $get('hygrostat'))
                                                    ->numeric()
                                                    ->label('Higrostat min'),
                                                Forms\Components\TextInput::make('hygrostat_max')
                                                    ->visible(fn(callable $get) => $get('hygrostat'))
                                                    ->numeric()
                                                    ->label('Higrostat max'),
                                                Forms\Components\Toggle::make('timer')
                                                    ->live()
                                                    ->label('Timer'),
                                                Forms\Components\TextInput::make('timer_min')
                                                    ->visible(fn(callable $get) => $get('timer'))
                                                    ->numeric()
                                                    ->label('Timer min'),
                                                Forms\Components\TextInput::make('timer_max')
                                                    ->visible(fn(callable $get) => $get('timer'))
                                                    ->numeric()
                                                    ->label('Timer max'),
                                            ]),
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Toggle::make('auto_mode')
                                                    ->live()
                                                    ->label('Tryb automatyczny'),
                                                Forms\Components\TextInput::make('auto_mode_min')
                                                    ->visible(fn(callable $get) => $get('auto_mode'))
                                                    ->numeric()
                                                    ->label('Tryb auto min'),
                                                Forms\Components\TextInput::make('auto_mode_max')
                                                    ->visible(fn(callable $get) => $get('auto_mode'))
                                                    ->numeric()
                                                    ->label('Tryb auto max'),
                                                Forms\Components\Toggle::make('night_mode')
                                                    ->live()
                                                    ->label('Tryb nocny'),
                                                Forms\Components\TextInput::make('night_mode_min')
                                                    ->visible(fn(callable $get) => $get('night_mode'))
                                                    ->numeric()
                                                    ->label('Tryb nocny min'),
                                                Forms\Components\TextInput::make('night_mode_max')
                                                    ->visible(fn(callable $get) => $get('night_mode'))
                                                    ->numeric()
                                                    ->label('Tryb nocny max'),
                                                Forms\Components\Toggle::make('child_lock')
                                                    ->live()
                                                    ->label('Blokada rodzicielska'),
                                                Forms\Components\TextInput::make('child_lock_min')
                                                    ->visible(fn(callable $get) => $get('child_lock'))
                                                    ->numeric()
                                                    ->label('Blokada rodzicielska min'),
                                                Forms\Components\TextInput::make('child_lock_max')
                                                    ->visible(fn(callable $get) => $get('child_lock'))
                                                    ->numeric()
                                                    ->label('Blokada rodzicielska max'),
                                                Forms\Components\Toggle::make('display')
                                                    ->live()
                                                    ->label('Wyświetlacz'),
                                                Forms\Components\TextInput::make('display_min')
                                                    ->visible(fn(callable $get) => $get('display'))
                                                    ->numeric()
                                                    ->label('Wyświetlacz min'),
                                                Forms\Components\TextInput::make('display_max')
                                                    ->visible(fn(callable $get) => $get('display'))
                                                    ->numeric()
                                                    ->label('Wyświetlacz max'),
                                                Forms\Components\Toggle::make('remote_control')
                                                    ->live()
                                                    ->label('Pilot'),
                                                Forms\Components\TextInput::make('remote_control_min')
                                                    ->visible(fn(callable $get) => $get('remote_control'))
                                                    ->numeric()
                                                    ->label('Pilot min'),
                                                Forms\Components\TextInput::make('remote_control_max')
                                                    ->visible(fn(callable $get) => $get('remote_control'))
                                                    ->numeric()
                                                    ->label('Pilot max'),
                                            ]),
                                    ]),
                                TagsInput::make('functions')
                                    ->separator(',')
                                    ->columnSpanFull()
                                    ->label('Funkcje'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Filtry')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Filtr ewaporacyjny')
                                            ->schema([
                                                Forms\Components\Toggle::make('evaporative_filter')
                                                    ->live()
                                                    ->label('Filtr ewaporacyjny'),
                                                Forms\Components\TextInput::make('evaporative_filter_life')
                                                    ->visible(fn(callable $get) => $get('evaporative_filter'))
                                                    ->numeric()
                                                    ->label('Żywotność filtra ewaporacyjnego'),
                                                Forms\Components\TextInput::make('evaporative_filter_price')
                                                    ->visible(fn(callable $get) => $get('evaporative_filter'))
                                                    ->numeric()
                                                    ->label('Cena filtra ewaporacyjnego'),
                                            ]),
                                        Forms\Components\Section::make('Srebrna jonizacja')
                                            ->schema([
                                                Forms\Components\Toggle::make('silver_ion')
                                                    ->live()
                                                    ->label('Srebrna jonizacja'),
                                                Forms\Components\TextInput::make('silver_ion_life')
                                                    ->visible(fn(callable $get) => $get('silver_ion'))
                                                    ->numeric()
                                                    ->label('Żywotność srebrnej jonizacji'),
                                                Forms\Components\TextInput::make('silver_ion_price')
                                                    ->visible(fn(callable $get) => $get('silver_ion'))
                                                    ->numeric()
                                                    ->label('Cena srebrnej jonizacji'),
                                            ]),
                                        Forms\Components\Section::make('Filtr ceramiczny')
                                            ->schema([
                                                Forms\Components\Toggle::make('ceramic_filter')
                                                    ->live()
                                                    ->label('Filtr ceramiczny'),
                                                Forms\Components\TextInput::make('ceramic_filter_life')
                                                    ->visible(fn(callable $get) => $get('ceramic_filter'))
                                                    ->numeric()
                                                    ->label('Żywotność filtra ceramicznego'),
                                                Forms\Components\TextInput::make('ceramic_filter_price')
                                                    ->visible(fn(callable $get) => $get('ceramic_filter'))
                                                    ->numeric()
                                                    ->label('Cena filtra ceramicznego'),
                                            ]),
                                        Forms\Components\Section::make('Inne filtry')
                                            ->schema([
                                                Forms\Components\Toggle::make('uv_lamp')
                                                    ->live()
                                                    ->label('Lampa UV'),
                                                Forms\Components\Toggle::make('ionization')
                                                    ->live()
                                                    ->label('Jonizacja'),
                                                Forms\Components\TextInput::make('hepa_filter_class')
                                                    ->label('Klasa filtra HEPA'),
                                                Forms\Components\Toggle::make('mesh_filter')
                                                    ->live()
                                                    ->label('Filtr siatkowy'),
                                                Forms\Components\Toggle::make('carbon_filter')
                                                    ->live()
                                                    ->label('Filtr węglowy'),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Funkcje smart')
                            ->schema([
                                Forms\Components\Section::make('Funkcje smart')
                                    ->schema([
                                        Forms\Components\Toggle::make('mobile_app')
                                            ->label('Aplikacja mobilna'),
                                        TagsInput::make('mobile_features')
                                            ->placeholder('Dodaj funkcję')
                                            ->separator(',')
                                            ->label('Funkcje aplikacji'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Zasilanie i wymiary')
                            ->schema([
                                Forms\Components\Section::make('Zasilanie i wymiary')
                                    ->schema([
                                        Forms\Components\TextInput::make('min_rated_power_consumption')
                                            ->numeric()
                                            ->label('Minimalny pobór mocy'),
                                        Forms\Components\TextInput::make('max_rated_power_consumption')
                                            ->numeric()
                                            ->label('Maksymalny pobór mocy'),
                                        Forms\Components\TextInput::make('rated_voltage')
                                            ->numeric()
                                            ->label('Napięcie znamionowe'),
                                        Forms\Components\TextInput::make('width')
                                            ->numeric()
                                            ->label('Szerokość'),
                                        Forms\Components\TextInput::make('height')
                                            ->numeric()
                                            ->label('Wysokość'),
                                        Forms\Components\TextInput::make('weight')
                                            ->numeric()
                                            ->label('Waga'),
                                        Forms\Components\TextInput::make('depth')
                                            ->numeric()
                                            ->label('Głębokość'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Kategorie')
                            ->schema([
                                Forms\Components\Section::make('Kategorie')
                                    ->schema([
                                        Forms\Components\Toggle::make('for_plant')
                                            ->label('Do roślin'),
                                        Forms\Components\Toggle::make('for_desk')
                                            ->label('Na biurko'),
                                        Forms\Components\Toggle::make('alergic')
                                            ->label('Dla alergików'),
                                        Forms\Components\Toggle::make('astmatic')
                                            ->label('Dla astmatyków'),
                                        Forms\Components\Toggle::make('small')
                                            ->label('Mały rozmiar'),
                                        Forms\Components\Toggle::make('for_kids')
                                            ->label('Dla dzieci'),
                                        Forms\Components\Toggle::make('big_area')
                                            ->label('Duża powierzchnia'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Ranking')
                            ->schema([
                                Forms\Components\Section::make('Ranking')
                                    ->schema([
                                        Forms\Components\TextInput::make('capability_points')
                                            ->numeric()
                                            ->label('Punkty za możliwości'),
                                        Forms\Components\TextInput::make('capability')
                                            ->label('Możliwości'),
                                        Forms\Components\TextInput::make('profitability_points')
                                            ->numeric()
                                            ->label('Punkty za opłacalność'),
                                        Forms\Components\TextInput::make('ranking')
                                            ->label('Ranking'),
                                        Forms\Components\TextInput::make('profitability')
                                            ->label('Opłacalność'),
                                        Forms\Components\Toggle::make('ranking_hidden')
                                            ->label('Ukryj w rankingu'),
                                        Forms\Components\Toggle::make('main_ranking')
                                            ->label('Ranking główny'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Dodatkowe')
                            ->schema([
                                Forms\Components\Section::make('Dodatkowe')
                                    ->schema([
                                        TagsInput::make('colors')
                                            ->placeholder('Dodaj kolor')
                                            ->separator(',')
                                            ->label('Kolory'),
                                        Forms\Components\TextInput::make('type_of_device')
                                            ->label('Typ urządzenia'),
                                        Forms\Components\Toggle::make('is_promo')
                                            ->label('Promocja'),
                                        TagsInput::make('gallery')
                                            ->placeholder('Dodaj zdjęcie')
                                            ->separator(',')
                                            ->label('Galeria'),
                                        Forms\Components\TextInput::make('Filter_cots_humi')
                                            ->label('Koszty filtrów'),
                                        Forms\Components\Toggle::make('disks')
                                            ->label('Dyski'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(null)
            ->headerActions([
                ImportAction::make()
                ->importer(AirHumidifierImporter::class),
                Tables\Actions\Action::make('Ustawienia')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                    'tableFilters' => [
                        'table_name' => [
                            'value' => 'air_humidifiers',
                        ],
                    ],
                ])),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('remote_id')
                    ->label('ID zdalne')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand_name')
                    ->label('Marka')
                    ->searchable(),
                TextInputColumn::make('price')
                    ->label('Cena')
                    ->width('50px')
                    ->extraInputAttributes(['step' => '0.01'])
                    ->afterStateUpdated(function ($record, $state): void {
                        Notification::make()
                            ->title('Cena została zaktualizowana')
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('max_performance')
                    ->label('Maksymalna wydajność')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_area')
                    ->label('Maksymalna powierzchnia')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('water_tank_capacity')
                    ->label('Pojemność zbiornika na wodę')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Zaktualizowano')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAirHumidifiers::route('/'),
            'create' => Pages\CreateAirHumidifier::route('/create'),
            'edit' => Pages\EditAirHumidifier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
