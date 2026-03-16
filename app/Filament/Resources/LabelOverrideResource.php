<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\LabelOverrideResource\Pages\ListLabelOverrides;
use App\Models\LabelOverride;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class LabelOverrideResource extends Resource
{
    protected static ?string $model = LabelOverride::class;

    protected static ?string $navigationLabel = 'Etykiety';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $pluralLabel = 'Etykiety';

    protected static ?string $label = 'Etykieta';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
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
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLabelOverrides::route('/'),
        ];
    }
}
