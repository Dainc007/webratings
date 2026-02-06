<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\FormSectionConfigurationResource\Pages\CreateFormSectionConfiguration;
use App\Filament\Resources\FormSectionConfigurationResource\Pages\EditFormSectionConfiguration;
use App\Filament\Resources\FormSectionConfigurationResource\Pages\ListFormSectionConfigurations;
use App\Models\FormSectionConfiguration;
use App\Models\FormTabConfiguration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class FormSectionConfigurationResource extends Resource
{
    protected static ?string $model = FormSectionConfiguration::class;

    protected static ?string $navigationLabel = 'Sekcje Formularza';

    protected static ?string $pluralLabel = 'Sekcje Formularza';

    protected static ?string $label = 'Sekcja Formularza';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('section_label')
                    ->label('Nazwa sekcji'),
                TextColumn::make('section_key')
                    ->label('Klucz')
                    ->color('gray'),
                TextColumn::make('tab_key')
                    ->label('Zakładka')
                    ->color('gray'),
                TextColumn::make('columns')
                    ->label('Kolumny'),
                TextColumn::make('sort_order')
                    ->label('Kolejność'),
                ToggleColumn::make('is_collapsible')
                    ->label('Zwijalna'),
                ToggleColumn::make('is_visible')
                    ->label('Widoczna'),
                TextColumn::make('depends_on')
                    ->label('Zależy od')
                    ->color('gray')
                    ->placeholder('-'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('table_name')
                    ->label('Produkt')
                    ->selectablePlaceholder(false)
                    ->default('air_purifiers')
                    ->options(Product::getOptions()),
                SelectFilter::make('tab_key')
                    ->label('Zakładka')
                    ->options(fn () => FormTabConfiguration::query()
                        ->distinct()
                        ->pluck('tab_label', 'tab_key')
                        ->toArray()),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormSectionConfigurations::route('/'),
            'create' => CreateFormSectionConfiguration::route('/create'),
            'edit' => EditFormSectionConfiguration::route('/{record}/edit'),
        ];
    }
}
