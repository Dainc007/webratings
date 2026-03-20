<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\LabelOverrideResource\Pages\CreateLabelOverride;
use App\Filament\Resources\LabelOverrideResource\Pages\ListLabelOverrides;
use App\Models\LabelOverride;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema as DBSchema;
use UnitEnum;

final class LabelOverrideResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = LabelOverride::class;

    protected static ?string $navigationLabel = 'Etykiety';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $pluralLabel = 'Etykiety';

    protected static ?string $label = 'Etykieta';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('table_name')
                ->label('Produkt')
                ->options(Product::getOptions())
                ->required()
                ->searchable()
                ->live(),
            Select::make('element_type')
                ->label('Typ')
                ->options([
                    'field' => 'Pole',
                    'tab' => 'Zakładka',
                    'section' => 'Sekcja',
                ])
                ->required()
                ->live(),
            Select::make('element_key')
                ->label('Oryginalna nazwa')
                ->required()
                ->searchable()
                ->options(function (Get $get): array {
                    $tableName = $get('table_name');
                    $elementType = $get('element_type');
                    if (! $tableName || ! $elementType) {
                        return [];
                    }

                    return self::getAvailableKeys($tableName, $elementType);
                })
                ->helperText('Wybierz istniejącą nazwę lub wpisz własną'),
            TextInput::make('display_label')
                ->label('Nowa nazwa')
                ->required(),
            TextInput::make('sort_order')
                ->label('Kolejność')
                ->numeric()
                ->nullable()
                ->helperText('Niższa wartość = wyżej w sekcji (CSS order)'),
        ]);
    }

    private static function getAvailableKeys(string $tableName, string $elementType): array
    {
        if ($elementType === 'field') {
            try {
                $columns = DBSchema::getColumnListing($tableName);

                return array_combine($columns, $columns);
            } catch (\Throwable) {
                return [];
            }
        }

        $map = [
            'air_purifiers' => [
                'tab' => ['Podstawowe informacje', 'Wydajność', 'Nawilżanie', 'Filtry', 'Funkcje', 'Wymiary', 'Klasyfikacja', 'Daty'],
                'section' => ['Podstawowe informacje', 'Oceny i ranking', 'Linki partnerskie', 'Ceneo', 'Galeria', 'Nawilżanie', 'Higrostat', 'Filtr ewaporacyjny', 'Filtr HEPA', 'Filtr węglowy', 'Jonizator', 'Inne funkcje', 'Czujniki'],
            ],
            'air_humidifiers' => [
                'tab' => ['Podstawowe informacje', 'Wydajność', 'Zbiornik na wodę', 'Sterowanie', 'Filtry', 'Wymiary', 'Kategorie', 'Dodatkowe'],
                'section' => ['Podstawowe informacje', 'Linki partnerskie', 'Linki Ceneo', 'Link do recenzji', 'Ranking', 'Galeria', 'Typy i kategorie', 'Wydajność', 'Głośność wentylatora', 'Pobór mocy', 'Zbiornik na wodę', 'Funkcje smart', 'Filtr ewaporacyjny', 'Srebrna jonizacja', 'Filtr ceramiczny', 'Filtr węglowy', 'Inne filtry', 'Wymiary', 'Kategorie', 'Dodatkowe'],
            ],
            'air_conditioners' => [
                'tab' => ['Podstawowe informacje', 'Wydajność chłodzenia', 'Wydajność grzania', 'Tryby pracy i funkcje', 'Filtry i oczyszczanie', 'Sterowanie i łączność', 'Specyfikacja techniczna', 'Dodatkowe informacje'],
                'section' => ['Podstawowe informacje', 'Linki partnerskie', 'Linki Ceneo', 'Link do recenzji', 'Galeria', 'Oceny i ranking', 'Dokumentacja', 'Parametry chłodzenia', 'Parametry grzania', 'Tryby pracy', 'Parametry powietrza', 'Hałas', 'Filtry podstawowe', 'Filtr HEPA', 'Filtr węglowy', 'Dodatkowe technologie', 'Sterowanie', 'Funkcje i wyposażenie', 'Chłodziwo', 'Parametry elektryczne', 'Wymiary i waga', 'Instalacja', 'Wygląd', 'Dane systemowe'],
            ],
            'dehumidifiers' => [
                'tab' => ['Podstawowe informacje', 'Wydajność osuszania', 'Warunki pracy', 'Zbiornik na wodę', 'Higrostat i sterowanie', 'Filtry i oczyszczanie', 'Sterowanie i łączność', 'Specyfikacja techniczna', 'Dodatkowe informacje'],
                'section' => ['Podstawowe informacje', 'Oceny i ranking', 'Linki partnerskie', 'Linki Ceneo', 'Link do recenzji', 'Galeria', 'Parametry osuszania', 'Zakres temperatur', 'Zakres wilgotności', 'Parametry zbiornika', 'Higrostat', 'Wentylator', 'Hałas', 'Tryby pracy', 'Filtry podstawowe', 'Filtr HEPA', 'Filtr węglowy', 'Dodatkowe technologie', 'Sterowanie', 'Funkcje i wyposażenie', 'Chłodziwo', 'Parametry elektryczne', 'Wymiary i waga', 'Dokumentacja', 'Dane systemowe'],
            ],
            'upright_vacuums' => [
                'tab' => ['Podstawowe informacje', 'Moc i wydajność', 'Zasilanie i bateria', 'Funkcje czyszczenia', 'Filtry i technologie', 'Szczotki i akcesoria', 'Wyświetlacz i sterowanie', 'Dodatkowe informacje'],
                'section' => ['Podstawowe informacje', 'Linki partnerskie', 'Linki Ceneo', 'Link do recenzji', 'Galeria', 'Oceny i ranking', 'Parametry ssania', 'Silnik', 'Zasilanie', 'Bateria', 'Funkcje mopowania', 'Zbiorniki', 'System filtracji', 'Dodatkowe technologie', 'Szczotki', 'Wyposażenie dodatkowe', 'Wyświetlacz', 'Czas pracy', 'Wygląd i wymiary', 'Przeznaczenie', 'Wideo'],
            ],
            'sensors' => [
                'tab' => ['Podstawowe informacje', 'Czujniki PM', 'Czujniki chemiczne', 'Czujniki środowiskowe', 'Zasilanie i łączność', 'Funkcje urządzenia', 'Wymiary i wydajność', 'Ranking', 'Metadane'],
                'section' => ['Podstawowe informacje', 'Linki partnerskie', 'Ceneo', 'Link do recenzji', 'Czujnik PM1', 'Czujnik PM2.5', 'Czujnik PM10', 'Czujnik LZO', 'Czujnik HCHO', 'Czujnik CO2', 'Czujnik CO', 'Czujnik temperatury', 'Czujnik wilgotności', 'Czujnik ciśnienia', 'Zasilanie', 'Łączność', 'Funkcje', 'Wymiary fizyczne', 'Ocena wydajności', 'Ustawienia rankingu', 'Identyfikatory systemowe', 'Znaczniki czasu'],
            ],
        ];

        $keys = $map[$tableName][$elementType] ?? [];

        return array_combine($keys, $keys);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('table_name')
                    ->label('Produkt')
                    ->badge(),
                TextColumn::make('element_type')
                    ->label('Typ')
                    ->badge(),
                TextColumn::make('element_key')
                    ->label('Klucz')
                    ->searchable(),
                TextInputColumn::make('display_label')
                    ->label('Wyświetlana nazwa'),
                TextInputColumn::make('sort_order')
                    ->label('Kolejność')
                    ->type('number')
                    ->rules(['nullable', 'integer']),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('table_name')
                    ->orderBy('element_type');
            })
            ->filters([
                SelectFilter::make('table_name')
                    ->label('Produkt')
                    ->options(Product::getOptions()),
                SelectFilter::make('element_type')
                    ->label('Typ')
                    ->options([
                        'field' => 'Pole',
                        'tab' => 'Zakładka',
                        'section' => 'Sekcja',
                    ]),
            ])
            ->filtersTriggerAction(
                fn (\Filament\Actions\Action $action) => $action->slideOver(),
            )
            ->recordActions([
                \Filament\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLabelOverrides::route('/'),
            'create' => CreateLabelOverride::route('/create'),
        ];
    }
}
