<?php

declare(strict_types=1);

namespace App\Filament\Resources;

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
                Tables\Columns\TextColumn::make('column_name')
                    ->label('Nazwa kolumny')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Kolejność')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->sortable()
                    ->label('Pokazuj w Tabeli')
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('table_name')
                    ->label('Typ Produktu')
                    ->options(function () {
                        return TableColumnPreference::distinct()
                            ->pluck('table_name')
                            ->mapWithKeys(function ($tableName) {
                                return [$tableName => __("tables.names.{$tableName}")];
                            })
                            ->toArray();
                    }),
            ])
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
