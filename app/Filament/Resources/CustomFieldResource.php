<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\CustomFieldStatus;
use App\Enums\Product;
use App\Filament\Resources\CustomFieldResource\Pages\CreateCustomField;
use App\Filament\Resources\CustomFieldResource\Pages\EditCustomField;
use App\Filament\Resources\CustomFieldResource\Pages\ListCustomFields;
use App\Models\CustomField;
use App\Models\FormLayoutItem;
use App\Services\CustomFieldService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

final class CustomFieldResource extends Resource
{
    protected static ?string $model = CustomField::class;

    protected static ?string $navigationLabel = 'Dodatkowe Pola';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static ?string $pluralLabel = 'Dodatkowe Pola';

    protected static ?string $label = 'Dodatkowe Pola';

    protected static string|UnitEnum|null $navigationGroup = 'Ustawienia';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('table_name')
                    ->options(Product::getOptions())
                    ->required()
                    ->live()
                    ->disabled(fn (?CustomField $record) => $record !== null),

                Select::make('column_type')
                    ->options([
                        'string' => 'Krótkie pole tekstowe (do 255 znaków)',
                        'integer' => 'Liczba',
                        'boolean' => 'TAK/NIE',
                    ])
                    ->required()
                    ->disabled(fn (?CustomField $record) => $record !== null),

                TextInput::make('column_name')
                    ->hintIconTooltip('np. nazwa w języku angielskim z małych liter pod jaką zapiszemy dane w bazie.')
                    ->required()
                    ->maxLength(255)
                    ->alphaDash()
                    ->disabled(fn (?CustomField $record) => $record !== null),

                TextInput::make('display_name')
                    ->required()
                    ->maxLength(255),

                Select::make('target_section')
                    ->label('Docelowa sekcja (opcjonalnie)')
                    ->options(function (Get $get): array {
                        $tableName = $get('table_name');
                        if (! $tableName) {
                            return [];
                        }

                        return FormLayoutItem::where('table_name', $tableName)
                            ->where('element_type', 'section')
                            ->orderBy('sort_order')
                            ->pluck('element_key', 'element_key')
                            ->toArray();
                    })
                    ->visible(fn (?CustomField $record) => $record === null)
                    ->hintIconTooltip('Wybierz sekcję, w której pole pojawi się w edytorze układu. Możesz to zmienić później.'),

                TextEntry::make('status_display')
                    ->label('Status')
                    ->visible(fn (?CustomField $record) => $record !== null)
                    ->state(fn (?CustomField $record) => $record?->status?->getLabel()),

                TextEntry::make('error_display')
                    ->label('Szczegóły błędu')
                    ->visible(fn (?CustomField $record) => $record?->status === CustomFieldStatus::FAILED)
                    ->state(fn (?CustomField $record) => $record?->error_message),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll(fn () => CustomField::pending()->exists() ? '3s' : null)
            ->modifyQueryUsing(fn ($query) => $query->whereNot('status', CustomFieldStatus::DELETING->value)->orWhere('status', CustomFieldStatus::DELETING->value))
            ->columns([
                TextColumn::make('display_name')
                    ->label('Nazwa'),
                TextColumn::make('table_name')
                    ->label('Tabela'),
                TextColumn::make('column_name')
                    ->label('Kolumna'),
                TextColumn::make('column_type')
                    ->label('Typ'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (CustomFieldStatus $state) => $state->getColor())
                    ->formatStateUsing(fn (CustomFieldStatus $state) => $state->getLabel()),
                TextColumn::make('error_message')
                    ->label('Błąd')
                    ->limit(50)
                    ->visible(fn () => CustomField::where('status', CustomFieldStatus::FAILED)->exists()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('retry')
                    ->label('Ponów')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (CustomField $record) => $record->status === CustomFieldStatus::FAILED)
                    ->action(fn (CustomField $record) => CustomFieldService::retryField($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListCustomFields::route('/'),
            'create' => CreateCustomField::route('/create'),
            'edit' => EditCustomField::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['display_name', 'table_name'];
    }

    public static function getNavigationBadge(): string
    {
        return (string) self::getModel()::where('status', CustomFieldStatus::ACTIVE)->count();
    }
}
