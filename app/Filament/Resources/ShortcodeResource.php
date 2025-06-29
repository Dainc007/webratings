<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\ShortcodeResource\Pages;
use App\Filament\Resources\ShortcodeResource\RelationManagers;
use App\Models\Shortcode;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class ShortcodeResource extends Resource
{
    protected static ?string $model = Shortcode::class;

    protected static ?string $navigationLabel = 'Shortcody';

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $pluralModelLabel = 'Shortcody';

    protected static ?string $pluralLabel = 'Shortcody';

    protected static ?string $modelLabel = 'Shortcode';

    protected static ?string $label = 'Shortcode';

    protected static ?string $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('Nazwa shortcode')),
                Select::make('product_types')
                    ->label(__('Typy produktów'))
                    ->multiple()
                    ->options(Product::getOptions())
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(__('Opis'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            RelationManagers\ConditionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortcodes::route('/'),
            'create' => Pages\CreateShortcode::route('/create'),
            'edit' => Pages\EditShortcode::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }
}
