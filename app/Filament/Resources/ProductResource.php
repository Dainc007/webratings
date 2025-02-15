<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon ='heroicon-o-shopping-cart';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('status')->label('Status')->required(),
                TextInput::make('brand_name')->label('Brand Name')->required(),
                TextInput::make('model')->label('Model')->required(),
                TextInput::make('price')->numeric()->label('Price')->required()->step(0.01),
            ]);
    }

        public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->label('Status')->sortable()->searchable(),
                TextColumn::make('brand_name')->label('Brand Name')->sortable()->searchable(),
                TextColumn::make('model')->label('Model')->sortable()->searchable(),
                TextColumn::make('price')->label('Price')->sortable()->searchable(),
            ])
            ->filters([
                // Dodaj filtry, jeśli są potrzebne
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make('Import Products')->importer(ProductImporter::class),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
