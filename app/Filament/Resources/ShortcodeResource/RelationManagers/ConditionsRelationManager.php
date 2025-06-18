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

class ConditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'conditions';
    protected static ?string $recordTitleAttribute = 'field';
    protected static ?string $title = 'Warunki';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('field')
                ->required()
                ->label('Pole'),
            Select::make('operator')
                ->options(ShortcodeOperator::optionsForSelect())
                ->required()
                ->label('Warunek'),
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