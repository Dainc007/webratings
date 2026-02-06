<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\FormFieldConfigurationResource\Pages\CreateFormFieldConfiguration;
use App\Filament\Resources\FormFieldConfigurationResource\Pages\EditFormFieldConfiguration;
use App\Filament\Resources\FormFieldConfigurationResource\Pages\ListFormFieldConfigurations;
use App\Models\FormFieldConfiguration;
use App\Models\FormSectionConfiguration;
use App\Models\FormTabConfiguration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class FormFieldConfigurationResource extends Resource
{
    protected static ?string $model = FormFieldConfiguration::class;

    protected static ?string $navigationLabel = 'Pola Formularza';

    protected static ?string $pluralLabel = 'Pola Formularza';

    protected static ?string $label = 'Pole Formularza';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('field_name')
                    ->label('Nazwa pola')
                    ->searchable(),
                SelectColumn::make('tab_key')
                    ->label('Zakładka')
                    ->options(fn ($record) => FormTabConfiguration::query()
                        ->where('table_name', $record->table_name ?? 'air_purifiers')
                        ->orderBy('sort_order')
                        ->pluck('tab_label', 'tab_key')
                        ->toArray())
                    ->searchable(),
                SelectColumn::make('section_key')
                    ->label('Sekcja')
                    ->options(fn ($record) => array_merge(
                        ['' => '-- Bez sekcji --'],
                        FormSectionConfiguration::query()
                            ->where('table_name', $record->table_name ?? 'air_purifiers')
                            ->where('tab_key', $record->tab_key ?? 'basic_info')
                            ->orderBy('sort_order')
                            ->pluck('section_label', 'section_key')
                            ->toArray()
                    ))
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->label('Kolejność'),
                ToggleColumn::make('is_visible')
                    ->label('Widoczne'),
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
            'index' => ListFormFieldConfigurations::route('/'),
            'create' => CreateFormFieldConfiguration::route('/create'),
            'edit' => EditFormFieldConfiguration::route('/{record}/edit'),
        ];
    }
}
