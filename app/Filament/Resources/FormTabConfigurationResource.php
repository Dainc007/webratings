<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\FormTabConfigurationResource\Pages\CreateFormTabConfiguration;
use App\Filament\Resources\FormTabConfigurationResource\Pages\EditFormTabConfiguration;
use App\Filament\Resources\FormTabConfigurationResource\Pages\ListFormTabConfigurations;
use App\Models\FormTabConfiguration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class FormTabConfigurationResource extends Resource
{
    protected static ?string $model = FormTabConfiguration::class;

    protected static ?string $navigationLabel = 'Układ Formularza';

    protected static ?string $pluralLabel = 'Układ Formularza';

    protected static ?string $label = 'Układ Formularza';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('tab_label')
                    ->label('Nazwa zakładki'),
                TextColumn::make('tab_key')
                    ->label('Klucz')
                    ->color('gray'),
                TextColumn::make('sort_order')
                    ->label('Kolejność'),
                ToggleColumn::make('is_visible')
                    ->label('Widoczna'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('table_name')
                    ->label('Produkt')
                    ->selectablePlaceholder(false)
                    ->default('air_purifiers')
                    ->options(Product::getOptions()),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([])
            ->toolbarActions([]);
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
            'index' => ListFormTabConfigurations::route('/'),
            'create' => CreateFormTabConfiguration::route('/create'),
            'edit' => EditFormTabConfiguration::route('/{record}/edit'),
        ];
    }
}
