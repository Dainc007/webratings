<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomFieldResource\Pages;
use App\Filament\Resources\CustomFieldResource\RelationManagers;
use App\Models\AirPurifier;
use App\Models\CustomField;
use App\Services\CustomFieldService;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class CustomFieldResource extends Resource
{
    protected static ?string $model = CustomField::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Produkty';
    protected static ?string $navigationLabel = 'Dodatkowe Pola';

    protected static ?string $pluralLabel = 'Dodatkowe Pola';

    protected static ?string $label = 'Dodatkowe Pola';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('table_name')
                    ->label('Produkt')
                    ->options([
                        'air_purifiers' => 'Oczyszczacz Powietrza',
                    ])
                    ->required(),



                Select::make('column_type')
                    ->label('Typ')
                    ->options([
                        'string' => 'Krótkie pole tekstowe (do 255 znaków)',
                        'integer' => 'Liczba',
                        'boolean' => 'Prawda/Fałsz',
                    ])
                    ->required(),

                TextInput::make('column_name')
                    ->columnSpanFull()
                    ->label('Nazwa Kolumny')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('column_name')->label('Nazwa Kolumny'),
                Tables\Columns\TextColumn::make('table_name')->label('Nazwa Tabeli'),
                Tables\Columns\TextColumn::make('column_name')->label('Nazwa Kolumny'),
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
}
