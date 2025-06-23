<?php
declare(strict_types=1);

namespace App\Filament\Resources\ShortcodeResource\RelationManagers;

use App\Models\ShortcodeCondition;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\CreateAction;
use App\Enums\ShortcodeOperator;
use Illuminate\Support\Facades\Schema;

class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';
    protected static ?string $recordTitleAttribute = 'field';
    protected static ?string $title = 'Warunki';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('field')
                ->required()
                ->label('Pole')
                ->searchable()
                ->getSearchResultsUsing(function (string $search) {
                    $shortcode = $this->getOwnerRecord();
                    $productTypes = $shortcode->product_types ?? [];
                    
                    $columns = collect();
                    
                    foreach ($productTypes as $type) {
                        $tableName = match ($type) {
                            'air_purifiers' => 'air_purifiers',
                            'air_humidifiers' => 'air_humidifiers',
                            default => null,
                        };
                        
                        if ($tableName && Schema::hasTable($tableName)) {
                            $tableColumns = Schema::getColumnListing($tableName);
                            foreach ($tableColumns as $column) {
                                if (str_contains(strtolower($column), strtolower($search))) {
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

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                TextColumn::make('field')->label('Pole')->searchable(),
                TextColumn::make('operator')->label('Warunek')->formatStateUsing(fn($state) => ShortcodeOperator::label($state)),
                TextColumn::make('value')->label('Wartość'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
} 