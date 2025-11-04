<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ShortcodeResource\RelationManagers\ConditionsRelationManager;
use App\Filament\Resources\ShortcodeResource\Pages\ListShortcodes;
use App\Filament\Resources\ShortcodeResource\Pages\CreateShortcode;
use App\Filament\Resources\ShortcodeResource\Pages\EditShortcode;
use App\Enums\Product;
use App\Filament\Resources\ShortcodeResource\Pages;
use App\Filament\Resources\ShortcodeResource\RelationManagers;
use App\Models\Shortcode;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class ShortcodeResource extends Resource
{
    protected static ?string $model = Shortcode::class;

    protected static ?string $navigationLabel = 'Shortcody';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $pluralModelLabel = 'Shortcody';

    protected static ?string $pluralLabel = 'Shortcody';

    protected static ?string $modelLabel = 'Shortcode';

    protected static ?string $label = 'Shortcode';

    protected static string | \UnitEnum | null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label(__('Nazwa shortcode')),
                Select::make('product_types')
                    ->label(__('Typy produktów'))
                    ->multiple()
                    ->options(Product::getOptions())
                    ->required(),
                Textarea::make('description')
                    ->label(__('Opis'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ConditionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShortcodes::route('/'),
            'create' => CreateShortcode::route('/create'),
            'edit' => EditShortcode::route('/{record}/edit'),
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
