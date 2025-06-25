<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DehumidifierResource\Pages;
use App\Filament\Resources\DehumidifierResource\RelationManagers;
use App\Models\Dehumidifier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\DehumidifierImporter;
use Filament\Tables\Actions\ImportAction;

class DehumidifierResource extends Resource
{
    protected static ?string $model = Dehumidifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye-dropper';
    protected static ?string $navigationLabel = 'Osuszacze Powietrza';
    protected static ?string $pluralLabel = 'Osuszacze Powietrza';
    protected static ?string $label = 'Osuszacz Powietrza';
    protected static ?string $navigationGroup = 'Produkty';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('remote_id')
                    ->numeric(),
                Forms\Components\TextInput::make('sort')
                    ->numeric(),
                Forms\Components\TextInput::make('user_created'),
                Forms\Components\DateTimePicker::make('date_created'),
                Forms\Components\TextInput::make('user_updated'),
                Forms\Components\DateTimePicker::make('date_updated'),
                Forms\Components\TextInput::make('brand_name'),
                Forms\Components\TextInput::make('model'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('price_before')
                    ->numeric(),
                Forms\Components\Textarea::make('discount_info')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('type'),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('partner_name'),
                Forms\Components\Textarea::make('partner_link_url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('partner_link_title'),
                Forms\Components\Textarea::make('ceneo_url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ceneo_link_title'),
                Forms\Components\TextInput::make('status'),
                Forms\Components\TextInput::make('max_performance_dry')
                    ->numeric(),
                Forms\Components\TextInput::make('other_performance_condition'),
                Forms\Components\TextInput::make('max_performance_dry_condition'),
                Forms\Components\TextInput::make('max_drying_area_manufacturer')
                    ->numeric(),
                Forms\Components\TextInput::make('other_performance_dry')
                    ->numeric(),
                Forms\Components\TextInput::make('max_drying_area_ro')
                    ->numeric(),
                Forms\Components\TextInput::make('weight')
                    ->numeric(),
                Forms\Components\TextInput::make('width')
                    ->numeric(),
                Forms\Components\TextInput::make('depth')
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->numeric(),
                Forms\Components\TextInput::make('minimum_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('maximum_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('minimum_humidity')
                    ->numeric(),
                Forms\Components\TextInput::make('maximum_humidity')
                    ->numeric(),
                Forms\Components\TextInput::make('rated_power_consumption')
                    ->numeric(),
                Forms\Components\TextInput::make('rated_voltage')
                    ->numeric(),
                Forms\Components\TextInput::make('refrigerant_kind'),
                Forms\Components\TextInput::make('refrigerant_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('needs_to_be_completed'),
                Forms\Components\Textarea::make('functions')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('water_tank_capacity')
                    ->numeric(),
                Forms\Components\TextInput::make('minimum_fill_time')
                    ->numeric(),
                Forms\Components\TextInput::make('average_filling_time')
                    ->numeric(),
                Forms\Components\Textarea::make('higrostat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('min_value_for_hygrostat')
                    ->numeric(),
                Forms\Components\TextInput::make('max_value_for_hygrostat')
                    ->numeric(),
                Forms\Components\TextInput::make('increment_of_the_hygrostat'),
                Forms\Components\TextInput::make('number_of_fan_speeds')
                    ->numeric(),
                Forms\Components\TextInput::make('max_air_flow')
                    ->numeric(),
                Forms\Components\TextInput::make('max_loudness')
                    ->numeric(),
                Forms\Components\TextInput::make('min_loudness')
                    ->numeric(),
                Forms\Components\Textarea::make('modes_of_operation')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('mesh_filter'),
                Forms\Components\TextInput::make('hepa_service_life')
                    ->numeric(),
                Forms\Components\TextInput::make('hepa_filter_price')
                    ->numeric(),
                Forms\Components\Toggle::make('hepa_filter'),
                Forms\Components\Toggle::make('carbon_filter'),
                Forms\Components\TextInput::make('carbon_service_life')
                    ->numeric(),
                Forms\Components\TextInput::make('carbon_filter_price')
                    ->numeric(),
                Forms\Components\Toggle::make('ionization'),
                Forms\Components\Toggle::make('uvc'),
                Forms\Components\Textarea::make('uv_light_generator')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('mobile_app'),
                Forms\Components\Textarea::make('mobile_features')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('partner_link_rel_2')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('ceneo_link_rel_2')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('manual_file'),
                Forms\Components\TextInput::make('capability_points')
                    ->numeric(),
                Forms\Components\TextInput::make('capability')
                    ->numeric(),
                Forms\Components\TextInput::make('profitability_points')
                    ->numeric(),
                Forms\Components\TextInput::make('profitability')
                    ->numeric(),
                Forms\Components\TextInput::make('ranking')
                    ->numeric(),
                Forms\Components\Textarea::make('review_link')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('ranking_hidden'),
                Forms\Components\Textarea::make('functions_and_equipment_dehumi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('main_ranking'),
                Forms\Components\Toggle::make('is_promo'),
                Forms\Components\Textarea::make('gallery')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->headerActions([
            Tables\Actions\ImportAction::make('Import Dehumidifiers')
                ->importer(DehumidifierImporter::class),
            Tables\Actions\Action::make('Ustawienia')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                    'tableFilters' => [
                        'table_name' => [
                            'value' => 'dehumidifiers',
                        ],
                    ],
                ])),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('remote_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_created')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_created')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_updated')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_updated')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_before')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('partner_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ceneo_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_performance_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('other_performance_condition')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_performance_dry_condition')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_drying_area_manufacturer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('other_performance_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_drying_area_ro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('depth')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_humidity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_humidity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_power_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('refrigerant_kind')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refrigerant_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('needs_to_be_completed')
                    ->searchable(),
                Tables\Columns\TextColumn::make('water_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_fill_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_filling_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_value_for_hygrostat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_value_for_hygrostat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('increment_of_the_hygrostat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_fan_speeds')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_air_flow')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_loudness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_loudness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('mesh_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hepa_service_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hepa_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('hepa_filter')
                    ->boolean(),
                Tables\Columns\IconColumn::make('carbon_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('carbon_service_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('carbon_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ionization')
                    ->boolean(),
                Tables\Columns\IconColumn::make('uvc')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mobile_app')
                    ->boolean(),
                Tables\Columns\TextColumn::make('manual_file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capability_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ranking')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ranking_hidden')
                    ->boolean(),
                Tables\Columns\TextColumn::make('main_ranking')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_promo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDehumidifiers::route('/'),
            'create' => Pages\CreateDehumidifier::route('/create'),
            'edit' => Pages\EditDehumidifier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
