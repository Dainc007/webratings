<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\CustomFieldResource\Pages\CreateCustomField;
use App\Filament\Resources\CustomFieldResource\Pages\EditCustomField;
use App\Filament\Resources\CustomFieldResource\Pages\ListCustomFields;
use App\Models\CustomField;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

final class CustomFieldResource extends Resource
{
    protected static ?string $model = CustomField::class;

    protected static ?string $navigationLabel = 'Dodatkowe Pola';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog'; // lub inna ikona

    protected static ?string $pluralLabel = 'Dodatkowe Pola';

    protected static ?string $label = 'Dodatkowe Pola';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('table_name')
                    ->options(Product::getOptions())
                    ->required(),

                Select::make('column_type')
                    ->options([
                        'string' => 'Krótkie pole tekstowe (do 255 znaków)',
                        'integer' => 'Liczba',
                        'boolean' => 'TAK/NIE',
                    ])
                    ->required(),

                TextInput::make('column_name')
                    ->hintIconTooltip('np. nazwa w języku angielskim z małych liter pod jaką zapiszemy dane w bazie.')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash(),

                TextInput::make('display_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name'),
                TextColumn::make('table_name'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomFields::route('/'),
            'create' => CreateCustomField::route('/create'),
            'edit' => EditCustomField::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['display_name', 'table_name'];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::count();
    }
}
