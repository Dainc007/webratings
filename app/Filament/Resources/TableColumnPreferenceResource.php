<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\TableColumnPreferenceResource\Pages;
use App\Models\TableColumnPreference;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class TableColumnPreferenceResource extends Resource
{
    protected static ?string $model = TableColumnPreference::class;

    protected static ?string $navigationLabel = 'Ustawienia';

    protected static ?string $pluralLabel = 'Ustawienia';

    protected static ?string $label = 'Ustawienia';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('column_name')->searchable(),
                Tables\Columns\TextColumn::make('sort_order'),
                Tables\Columns\ToggleColumn::make('is_visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('table_name')
                    ->label('Tabela')
                    ->selectablePlaceholder(false)
                    ->default('air_purifiers')
                    ->options(Product::getOptions()),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListTableColumnPreferences::route('/'),
            'create' => Pages\CreateTableColumnPreference::route('/create'),
            'edit' => Pages\EditTableColumnPreference::route('/{record}/edit'),
        ];
    }
}
