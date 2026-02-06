<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Imports\UprightVacuumImporter;
use App\Filament\Resources\UprightVacuumResource\Pages\CreateUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\EditUprightVacuum;
use App\Filament\Resources\UprightVacuumResource\Pages\ListUprightVacuums;
use App\Models\UprightVacuum;
use App\Services\CustomFieldService;
use App\Services\ExportActionService;
use App\Services\FormLayoutService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
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

final class UprightVacuumResource extends Resource
{
    protected static ?string $model = UprightVacuum::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Odkurzacze Pionowe';

    protected static ?string $pluralLabel = 'Odkurzacze Pionowe';

    protected static ?string $label = 'Odkurzacz Pionowy';

    protected static string|UnitEnum|null $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 6;

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
                ->label('Typ odkurzacza')
                ->options([
                    'pionowy' => 'Pionowy',
                    'reczny' => 'Ręczny',
                    '2w1' => '2w1 (pionowy + ręczny)',
                    'myjacy' => 'Myjący',
                    'workowy' => 'Workowy',
                    'bezworkowy' => 'Bezworkowy',
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

            'vacuum_cleaner_type' => fn () => Select::make('vacuum_cleaner_type')
                ->multiple()
                ->options([
                    'bezworkowy' => 'Bezworkowy',
                    'workowy' => 'Workowy',
                ])
                ->label('Typ odkurzacza'),

            'suction_power_aw' => fn () => TextInput::make('suction_power_aw')
                ->numeric()
                ->suffix('AW')
                ->label('Moc ssania (AW)'),

            'suction_power_pa' => fn () => TextInput::make('suction_power_pa')
                ->numeric()
                ->suffix('Pa')
                ->label('Moc ssania (Pa)'),

            'number_of_suction_power_levels' => fn () => TextInput::make('number_of_suction_power_levels')
                ->numeric()
                ->label('Liczba poziomów mocy ssania'),

            'automatic_power_adjustment' => fn () => TextInput::make('automatic_power_adjustment')
                ->label('Automatyczna regulacja mocy'),

            'suction_power_highest_level_pa' => fn () => TextInput::make('suction_power_highest_level_pa')
                ->numeric()
                ->suffix('Pa')
                ->label('Moc ssania - najwyższy poziom'),

            'suction_power_medium_level_pa' => fn () => TextInput::make('suction_power_medium_level_pa')
                ->numeric()
                ->suffix('Pa')
                ->label('Moc ssania - średni poziom'),

            'suction_power_low_level_pa' => fn () => TextInput::make('suction_power_low_level_pa')
                ->numeric()
                ->suffix('Pa')
                ->label('Moc ssania - niski poziom'),

            'maximum_engine_power' => fn () => TextInput::make('maximum_engine_power')
                ->numeric()
                ->suffix('W')
                ->label('Maksymalna moc silnika'),

            'rotation_speed' => fn () => TextInput::make('rotation_speed')
                ->numeric()
                ->suffix('obr/min')
                ->label('Prędkość obrotowa'),

            'noise_level' => fn () => TextInput::make('noise_level')
                ->numeric()
                ->suffix('dB')
                ->label('Poziom hałasu'),

            'power_supply' => fn () => Select::make('power_supply')
                ->multiple()
                ->options([
                    'Akumulatorowe' => 'Akumulatorowe',
                    'Sieciowe' => 'Sieciowe',
                ])
                ->label('Typ zasilania')
                ->live(),

            'cable_length' => fn () => TextInput::make('cable_length')
                ->numeric()
                ->suffix('m')
                ->label('Długość kabla')
                ->visible(fn (callable $get) => in_array('Sieciowe', $get('power_supply') ?? [])),

            'battery_change' => fn () => Select::make('battery_change')
                ->label('Wymiana baterii')
                ->options([
                    'tak' => 'Tak - możliwa wymiana',
                    'nie' => 'Nie - bateria wbudowana',
                    'ograniczona' => 'Ograniczona - wymaga serwisu',
                ]),

            'maximum_operation_time' => fn () => TextInput::make('maximum_operation_time')
                ->label('Maksymalny czas pracy'),

            'battery_charging_time' => fn () => TextInput::make('battery_charging_time')
                ->label('Czas ładowania baterii'),

            'battery_voltage' => fn () => TextInput::make('battery_voltage')
                ->numeric()
                ->suffix('V')
                ->label('Napięcie baterii'),

            'battery_capacity' => fn () => TextInput::make('battery_capacity')
                ->numeric()
                ->suffix('mAh')
                ->label('Pojemność baterii'),

            'operation_time_turbo' => fn () => TextInput::make('operation_time_turbo')
                ->label('Czas pracy - tryb turbo'),

            'operation_time_eco' => fn () => TextInput::make('operation_time_eco')
                ->label('Czas pracy - tryb eco'),

            'displaying_battery_status' => fn () => Select::make('displaying_battery_status')
                ->label('Wyświetlanie stanu baterii')
                ->options([
                    'diody_led' => 'Diody LED',
                    'wyswietlacz_lcd' => 'Wyświetlacz LCD',
                    'wyswietlacz_led' => 'Wyświetlacz LED',
                    'procent' => 'Procent na wyświetlaczu',
                    'brak' => 'Brak wskaźnika',
                ]),

            'mopping_function' => fn () => Toggle::make('mopping_function')
                ->label('Funkcja mopowania'),

            'active_washing_function' => fn () => Toggle::make('active_washing_function')
                ->label('Aktywna funkcja mycia'),

            'self_cleaning_function' => fn () => Toggle::make('self_cleaning_function')
                ->label('Funkcja samoczyszczenia'),

            'self_cleaning_underlays' => fn () => Toggle::make('self_cleaning_underlays')
                ->label('Samoczyszczenie podkładek'),

            'mopping_time_max' => fn () => TextInput::make('mopping_time_max')
                ->numeric()
                ->suffix('min')
                ->label('Maksymalny czas mopowania'),

            'type_of_washing' => fn () => Select::make('type_of_washing')
                ->label('Typ mycia')
                ->multiple()
                ->options([
                    'suche' => 'Suche',
                    'mokre' => 'Mokre',
                    'parowe' => 'Parowe',
                    'hybrydowe' => 'Hybrydowe (suche + mokre)',
                ]),

            'clean_water_tank_capacity' => fn () => TextInput::make('clean_water_tank_capacity')
                ->numeric()
                ->suffix('l')
                ->label('Pojemność zbiornika czystej wody'),

            'dirty_water_tank_capacity' => fn () => TextInput::make('dirty_water_tank_capacity')
                ->numeric()
                ->suffix('l')
                ->label('Pojemność zbiornika brudnej wody'),

            'dust_tank_capacity' => fn () => TextInput::make('dust_tank_capacity')
                ->numeric()
                ->suffix('l')
                ->label('Pojemność zbiornika na kurz'),

            'easy_emptying_tank' => fn () => TextInput::make('easy_emptying_tank')
                ->label('Łatwe opróżnianie zbiornika'),

            'pollution_filtration_system' => fn () => Select::make('pollution_filtration_system')
                ->label('System filtracji zanieczyszczeń')
                ->options([
                    'cyklonowy' => 'Cyklonowy',
                    'wielocyklonowy' => 'Wielocyklonowy',
                    'workowy' => 'Workowy',
                    'wodny' => 'Wodny',
                    'inny' => 'Inny',
                ]),

            'cyclone_technology' => fn () => Toggle::make('cyclone_technology')
                ->label('Technologia cyklonowa'),

            'mesh_filter' => fn () => Toggle::make('mesh_filter')
                ->label('Filtr wstępny'),

            'hepa_filter' => fn () => Toggle::make('hepa_filter')
                ->label('Filtr HEPA'),

            'epa_filter' => fn () => Toggle::make('epa_filter')
                ->label('Filtr EPA'),

            'uv_technology' => fn () => Toggle::make('uv_technology')
                ->label('Technologia UV'),

            'led_backlight' => fn () => Toggle::make('led_backlight')
                ->label('Podświetlenie LED'),

            'detecting_dirt_on_the_floor' => fn () => Toggle::make('detecting_dirt_on_the_floor')
                ->label('Wykrywanie brudu na podłodze'),

            'detecting_carpet' => fn () => Toggle::make('detecting_carpet')
                ->label('Wykrywanie dywanu'),

            'electric_brush' => fn () => Toggle::make('electric_brush')
                ->label('Elektroszczotka'),

            'turbo_brush' => fn () => Toggle::make('turbo_brush')
                ->label('Turboszczotka'),

            'carpet_and_floor_brush' => fn () => Toggle::make('carpet_and_floor_brush')
                ->label('Szczotka do dywanów i podłóg'),

            'attachment_for_pets' => fn () => Toggle::make('attachment_for_pets')
                ->label('Końcówka dla zwierząt'),

            'bendable_pipe' => fn () => Toggle::make('bendable_pipe')
                ->label('Giętka rura'),

            'telescopic_tube' => fn () => Toggle::make('telescopic_tube')
                ->label('Rura teleskopowa'),

            'hand_vacuum_cleaner' => fn () => Toggle::make('hand_vacuum_cleaner')
                ->label('Odkurzacz ręczny'),

            'charging_station' => fn () => Select::make('charging_station')
                ->label('Stacja ładująca')
                ->options([
                    'brak' => 'Brak',
                    'scienna' => 'Ścienna',
                    'stojaca' => 'Stojąca',
                    'stacja_dokujaca' => 'Stacja dokująca',
                    'podstawka' => 'Podstawka ładująca',
                ]),

            'additional_equipment' => fn () => TagsInput::make('additional_equipment')
                ->label('Dodatkowe wyposażenie')
                ->columnSpanFull(),

            'continuous_work' => fn () => Toggle::make('continuous_work')
                ->label('Praca ciągła'),

            'display' => fn () => Toggle::make('display')
                ->label('Wyświetlacz'),

            'display_type' => fn () => Select::make('display_type')
                ->multiple()
                ->options([
                    'LCD' => 'LCD',
                    'LED' => 'LED',
                    'TFT' => 'TFT',
                    'OLED' => 'OLED',
                    'cyfrowy' => 'Cyfrowy',
                ])
                ->label('Typ wyświetlacza'),

            'vacuuming_time_max' => fn () => TextInput::make('vacuuming_time_max')
                ->label('Maksymalny czas odkurzania'),

            'warranty' => fn () => TextInput::make('warranty')
                ->numeric()
                ->suffix('miesięcy')
                ->label('Gwarancja'),

            'colors' => fn () => TagsInput::make('colors')
                ->label('Dostępne kolory')
                ->columnSpanFull(),

            'weight' => fn () => TextInput::make('weight')
                ->numeric()
                ->suffix('kg')
                ->label('Waga'),

            'weight_hand' => fn () => TextInput::make('weight_hand')
                ->numeric()
                ->suffix('kg')
                ->label('Waga w ręce'),

            'for_pet_owners' => fn () => Toggle::make('for_pet_owners')
                ->label('Dla właścicieli zwierząt'),

            'for_allergy_sufferers' => fn () => Toggle::make('for_allergy_sufferers')
                ->label('Dla alergików'),

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

            'videorecenzja1' => fn () => TextInput::make('videorecenzja1')
                ->label('Link do wideo recenzji'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $customFieldSchema = CustomFieldService::getFormFields('upright_vacuums');

        return $schema
            ->components([
                Tabs::make('Formularz Odkurzacza Pionowego')
                    ->columnSpanFull()
                    ->tabs(FormLayoutService::buildForm('upright_vacuums', static::getFieldDefinitions(), $customFieldSchema)),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = CustomFieldService::getTableColumns('upright_vacuums');

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->headerActions([
                ImportAction::make('Import Upright Vacuums')
                    ->importer(UprightVacuumImporter::class),
                ExportActionService::createExportAllAction('upright_vacuums'),
                Action::make('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'upright_vacuums',
                            ],
                        ],
                    ])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportActionService::createExportBulkAction('upright_vacuums'),
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
            'index' => ListUprightVacuums::route('/'),
            'create' => CreateUprightVacuum::route('/create'),
            'edit' => EditUprightVacuum::route('/{record}/edit'),
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
