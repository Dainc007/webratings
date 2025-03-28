<?php

declare(strict_types=1);
//
// declare(strict_types=1);
//
// namespace App\Filament\Resources;
//
// use App\Filament\Imports\BrandImporter;
// use App\Filament\Resources\BrandResource\Pages;
// use App\Models\Brand;
// use Filament\Forms\Components\TextInput;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Table;
//
// final class BrandResource extends Resource
// {
//    protected static ?string $model = Brand::class;
//
//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
//
//    protected static ?string $navigationLabel = 'Marki';
//
//    protected static ?string $pluralLabel = 'Marki';
//
//    protected static ?string $label = 'Marki';
//
//    protected static ?string $navigationGroup = 'Produkty';
//
//    protected static ?int $navigationSort = 2;
//
//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                TextInput::make('name')->label(__('brand.name'))->required()->unique(),
//            ]);
//    }
//
//    public static function table(Table $table): Table
//    {
//        return $table
//            ->columns([
//                TextColumn::make('name')->label(__('brand.name'))->sortable()->searchable(),
//            ])
//            ->filters([
//                //
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
//            ])
//            ->headerActions([
//                Tables\Actions\ImportAction::make('Import Products')->label('Importuj Marki')->importer(BrandImporter::class),
//            ])
//            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
//            ]);
//    }
//
//    public static function getPages(): array
//    {
//        return [
//            'index' => Pages\ManageBrands::route('/'),
//        ];
//    }
// }
