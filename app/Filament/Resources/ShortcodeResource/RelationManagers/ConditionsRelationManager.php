<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShortcodeResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Enums\Product;
use App\Enums\ShortcodeOperator;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Schema;

final class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';

    protected static ?string $recordTitleAttribute = 'field';

    protected static ?string $title = 'Warunki';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            Select::make('field')
                ->required()
                ->label('Pole')
                ->searchable()
                ->getSearchResultsUsing(function (string $search) {
                    $shortcode = $this->getOwnerRecord();
                    $productTypes = $shortcode->product_types ?? [];

                    $columns = collect();

                    foreach ($productTypes as $type) {
                        $tableName = Product::tryFrom($type)?->value;

                        if ($tableName && Schema::hasTable($tableName)) {
                            $tableColumns = Schema::getColumnListing($tableName);
                            foreach ($tableColumns as $column) {
                                if (str_contains(mb_strtolower($column), mb_strtolower($search))) {
                                    $columns->put($column, $column);
                                }
                            }
                        }
                    }

                    return $columns->take(50)->toArray();
                })
                ->getOptionLabelUsing(fn ($value): string => $value),
            Select::make('operator')
                ->options(ShortcodeOperator::optionsForSelect())
                ->required()
                ->label('Operator'),
            TextInput::make('value')
                ->required()
                ->label('Wartość'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                TextColumn::make('field')->label('Pole')->searchable(),
                TextColumn::make('operator')->label('Warunek')->formatStateUsing(fn ($state): string => ShortcodeOperator::label($state)),
                TextColumn::make('value')->label('Wartość'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
