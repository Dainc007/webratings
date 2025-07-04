<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\CustomFieldResource\Pages;
use App\Models\CustomField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class CustomFieldResource extends Resource
{
    protected static ?string $model = CustomField::class;

    protected static ?string $navigationLabel = 'Dodatkowe Pola';

    protected static ?string $navigationIcon = 'heroicon-o-cog'; // lub inna ikona

    protected static ?string $pluralLabel = 'Dodatkowe Pola';

    protected static ?string $label = 'Dodatkowe Pola';

    protected static ?string $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('display_name'),
                Tables\Columns\TextColumn::make('table_name'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomFields::route('/'),
            'create' => Pages\CreateCustomField::route('/create'),
            'edit' => Pages\EditCustomField::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['display_name', 'table_name'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }
}
