<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Product;
use App\Filament\Resources\FormLayoutItemResource\Pages\ListFormLayoutItems;
use App\Models\FormLayoutItem;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class FormLayoutItemResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = FormLayoutItem::class;

    protected static ?string $navigationLabel = 'Układ formularza';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $pluralLabel = 'Układ formularza';

    protected static ?string $label = 'Element układu';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('table_name')
                ->label('Produkt')
                ->options(Product::getOptions())
                ->required()
                ->searchable()
                ->live(),
            Select::make('element_type')
                ->label('Typ')
                ->options([
                    'tab' => 'Zakładka',
                    'section' => 'Sekcja',
                    'field' => 'Pole',
                ])
                ->required()
                ->live(),
            TextInput::make('element_key')
                ->label('Klucz')
                ->required(),
            Select::make('parent_key')
                ->label('Rodzic')
                ->options(function (Get $get): array {
                    $tableName = $get('table_name');
                    $elementType = $get('element_type');
                    if (! $tableName || ! $elementType) {
                        return [];
                    }

                    $parentType = match ($elementType) {
                        'section' => 'tab',
                        'field' => 'section',
                        default => null,
                    };

                    if ($parentType === null) {
                        return [];
                    }

                    return FormLayoutItem::where('table_name', $tableName)
                        ->where('element_type', $parentType)
                        ->pluck('element_key', 'element_key')
                        ->toArray();
                })
                ->searchable()
                ->nullable()
                ->helperText('Zakładka nie ma rodzica. Sekcja -> rodzic to zakładka. Pole -> rodzic to sekcja.'),
            TextInput::make('sort_order')
                ->label('Kolejność')
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('table_name')
                    ->label('Produkt')
                    ->badge(),
                TextColumn::make('element_type')
                    ->label('Typ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tab' => 'primary',
                        'section' => 'warning',
                        'field' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('element_key')
                    ->label('Klucz')
                    ->searchable(),
                TextColumn::make('parent_key')
                    ->label('Rodzic')
                    ->searchable(),
                TextInputColumn::make('sort_order')
                    ->label('Kolejność')
                    ->type('number')
                    ->rules(['required', 'integer']),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('table_name')
                    ->orderBy('element_type')
                    ->orderBy('sort_order');
            })
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('table_name')
                    ->label('Produkt')
                    ->options(Product::getOptions()),
                SelectFilter::make('element_type')
                    ->label('Typ')
                    ->options([
                        'tab' => 'Zakładka',
                        'section' => 'Sekcja',
                        'field' => 'Pole',
                    ]),
                SelectFilter::make('parent_key')
                    ->label('Rodzic')
                    ->options(fn () => FormLayoutItem::distinct()
                        ->whereNotNull('parent_key')
                        ->pluck('parent_key', 'parent_key')
                        ->toArray()
                    ),
            ])
            ->filtersTriggerAction(
                fn (\Filament\Actions\Action $action) => $action->slideOver(),
            )
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormLayoutItems::route('/'),
        ];
    }
}
