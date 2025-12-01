<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\TableColumnPreferenceResource\Pages\CreateTableColumnPreference;
use App\Filament\Resources\TableColumnPreferenceResource\Pages\EditTableColumnPreference;
use App\Filament\Resources\TableColumnPreferenceResource\Pages\ListTableColumnPreferences;
use App\Models\TableColumnPreference;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class TableColumnPreferenceResource extends Resource
{
    protected static ?string $model = TableColumnPreference::class;

    protected static ?string $navigationLabel = 'Ustawienia';

    protected static ?string $pluralLabel = 'Ustawienia';

    protected static ?string $label = 'Ustawienia';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('column_name')->searchable(),
                TextColumn::make('sort_order'),
                ToggleColumn::make('is_visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('table_name')
                    ->label('Tabela')
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
            'index' => ListTableColumnPreferences::route('/'),
            'create' => CreateTableColumnPreference::route('/create'),
            'edit' => EditTableColumnPreference::route('/{record}/edit'),
        ];
    }
}
