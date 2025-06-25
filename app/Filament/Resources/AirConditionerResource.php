<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirConditionerResource\Pages;
use App\Filament\Resources\AirConditionerResource\RelationManagers;
use App\Models\AirConditioner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\AirConditionerImporter;
use Filament\Tables\Actions\ImportAction;

class AirConditionerResource extends Resource
{
    protected static ?string $model = AirConditioner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Klimatyzatory';
    protected static ?string $pluralLabel = 'Klimatyzatory';
    protected static ?string $label = 'Klimatyzator';
    protected static ?string $navigationGroup = 'Produkty';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('remote_id')
                    ->numeric(),
                Forms\Components\TextInput::make('status'),
                Forms\Components\TextInput::make('sort')
                    ->numeric(),
                Forms\Components\TextInput::make('user_created'),
                Forms\Components\DateTimePicker::make('date_created'),
                Forms\Components\TextInput::make('user_updated'),
                Forms\Components\DateTimePicker::make('date_updated'),
                Forms\Components\TextInput::make('brand_name'),
                Forms\Components\TextInput::make('model'),
                Forms\Components\TextInput::make('type'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('price_before')
                    ->numeric(),
                Forms\Components\Textarea::make('discount_info')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('partner_name'),
                Forms\Components\Textarea::make('partner_link_url')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('partner_link_rel_2')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('partner_link_title'),
                Forms\Components\Textarea::make('ceneo_url')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('ceneo_link_rel_2')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ceneo_link_title'),
                Forms\Components\TextInput::make('maximum_cooling_power')
                    ->numeric(),
                Forms\Components\TextInput::make('max_cooling_area_manufacturer')
                    ->numeric(),
                Forms\Components\TextInput::make('max_cooling_area_ro')
                    ->numeric(),
                Forms\Components\TextInput::make('maximum_heating_power')
                    ->numeric(),
                Forms\Components\TextInput::make('max_heating_area_manufacturer')
                    ->numeric(),
                Forms\Components\TextInput::make('max_heating_area_ro')
                    ->numeric(),
                Forms\Components\TextInput::make('usage'),
                Forms\Components\Textarea::make('colors')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('max_loudness')
                    ->numeric(),
                Forms\Components\TextInput::make('min_loudness')
                    ->numeric(),
                Forms\Components\TextInput::make('swing'),
                Forms\Components\TextInput::make('max_air_flow')
                    ->numeric(),
                Forms\Components\TextInput::make('number_of_fan_speeds')
                    ->numeric(),
                Forms\Components\TextInput::make('max_performance_dry')
                    ->numeric(),
                Forms\Components\TextInput::make('temperature_range'),
                Forms\Components\TextInput::make('max_cooling_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('min_cooling_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('min_heating_temperature')
                    ->numeric(),
                Forms\Components\TextInput::make('max_heating_temperature')
                    ->numeric(),
                Forms\Components\Toggle::make('mesh_filter'),
                Forms\Components\Toggle::make('hepa_filter'),
                Forms\Components\TextInput::make('hepa_filter_price')
                    ->numeric(),
                Forms\Components\TextInput::make('hepa_service_life')
                    ->numeric(),
                Forms\Components\Toggle::make('carbon_filter'),
                Forms\Components\TextInput::make('carbon_filter_price')
                    ->numeric(),
                Forms\Components\TextInput::make('carbon_service_life')
                    ->numeric(),
                Forms\Components\Toggle::make('ionization'),
                Forms\Components\Toggle::make('uvc'),
                Forms\Components\Textarea::make('uv_light_generator')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('mobile_app'),
                Forms\Components\Textarea::make('mobile_features')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('remote_control'),
                Forms\Components\Textarea::make('functions')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('refrigerant_kind'),
                Forms\Components\TextInput::make('needs_to_be_completed'),
                Forms\Components\TextInput::make('refrigerant_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('rated_voltage')
                    ->numeric(),
                Forms\Components\TextInput::make('rated_power_heating_consumption')
                    ->numeric(),
                Forms\Components\TextInput::make('rated_power_cooling_consumption')
                    ->numeric(),
                Forms\Components\TextInput::make('eer')
                    ->numeric(),
                Forms\Components\TextInput::make('cop')
                    ->numeric(),
                Forms\Components\TextInput::make('cooling_energy_class'),
                Forms\Components\TextInput::make('heating_energy_class'),
                Forms\Components\TextInput::make('width')
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->numeric(),
                Forms\Components\TextInput::make('depth')
                    ->numeric(),
                Forms\Components\TextInput::make('weight')
                    ->numeric(),
                Forms\Components\TextInput::make('manual'),
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
                Forms\Components\Toggle::make('discharge_pipe'),
                Forms\Components\TextInput::make('discharge_pipe_length')
                    ->numeric(),
                Forms\Components\TextInput::make('discharge_pipe_diameter')
                    ->numeric(),
                Forms\Components\TextInput::make('sealing'),
                Forms\Components\Toggle::make('drain_hose'),
                Forms\Components\Toggle::make('mode_cooling'),
                Forms\Components\Toggle::make('mode_heating'),
                Forms\Components\Toggle::make('mode_dry'),
                Forms\Components\Toggle::make('mode_fan'),
                Forms\Components\Toggle::make('mode_purify'),
                Forms\Components\TextInput::make('max_performance_dry_condition'),
                Forms\Components\Toggle::make('ranking_hidden'),
                Forms\Components\Textarea::make('functions_and_equipment_condi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('small'),
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
            ImportAction::make()
            ->importer(AirConditionerImporter::class),
            Tables\Actions\Action::make('Ustawienia')
            ->icon('heroicon-o-cog-6-tooth')
            ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                'tableFilters' => [
                    'table_name' => [
                        'value' => 'air_conditioners',
                    ],
                ],
            ])),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('remote_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_before')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('partner_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ceneo_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('maximum_cooling_power')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_cooling_area_manufacturer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_cooling_area_ro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_heating_power')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_heating_area_manufacturer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_heating_area_ro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_loudness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_loudness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('swing')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_air_flow')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_fan_speeds')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_performance_dry')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_cooling_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_cooling_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_heating_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_heating_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('mesh_filter')
                    ->boolean(),
                Tables\Columns\IconColumn::make('hepa_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hepa_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hepa_service_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('carbon_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('carbon_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('carbon_service_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ionization')
                    ->boolean(),
                Tables\Columns\IconColumn::make('uvc')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mobile_app')
                    ->boolean(),
                Tables\Columns\IconColumn::make('remote_control')
                    ->boolean(),
                Tables\Columns\TextColumn::make('refrigerant_kind')
                    ->searchable(),
                Tables\Columns\TextColumn::make('needs_to_be_completed')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refrigerant_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_power_heating_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_power_cooling_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('eer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cop')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cooling_energy_class')
                    ->searchable(),
                Tables\Columns\TextColumn::make('heating_energy_class')
                    ->searchable(),
                Tables\Columns\TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('depth')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('manual')
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
                Tables\Columns\IconColumn::make('discharge_pipe')
                    ->boolean(),
                Tables\Columns\TextColumn::make('discharge_pipe_length')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discharge_pipe_diameter')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sealing')
                    ->searchable(),
                Tables\Columns\IconColumn::make('drain_hose')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mode_cooling')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mode_heating')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mode_dry')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mode_fan')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mode_purify')
                    ->boolean(),
                Tables\Columns\TextColumn::make('max_performance_dry_condition')
                    ->searchable(),
                Tables\Columns\IconColumn::make('ranking_hidden')
                    ->boolean(),
                Tables\Columns\TextColumn::make('small')
                    ->searchable(),
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
            'index' => Pages\ListAirConditioners::route('/'),
            'create' => Pages\CreateAirConditioner::route('/create'),
            'edit' => Pages\EditAirConditioner::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
