<?php

namespace App\Filament\Resources;

use App\Filament\Imports\AirHumidifierImporter;
use App\Filament\Resources\AirHumidifierResource\Pages;
use App\Filament\Resources\AirHumidifierResource\RelationManagers;
use App\Models\AirHumidifier;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AirHumidifierResource extends Resource
{
    protected static ?string $model = AirHumidifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Nawilżacze Powietrza';

    protected static ?string $pluralLabel = 'Nawilżacze Powietrza';

    protected static ?string $label = 'Nawilżacze Powietrza';

    protected static ?string $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('user_created')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('date_created'),
                Forms\Components\TextInput::make('user_updated')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('date_updated'),
                Forms\Components\TextInput::make('brand_name')
                    ->required(),
                Forms\Components\TextInput::make('model')
                    ->required(),
                Forms\Components\TextInput::make('type'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('price_before')
                    ->numeric(),
                Forms\Components\Textarea::make('discount_info')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('partner_name'),
                Forms\Components\Textarea::make('partner_link_url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('partner_link_rel_2'),
                Forms\Components\TextInput::make('partner_link_title'),
                Forms\Components\TextInput::make('ceneo_link_rel_2'),
                Forms\Components\Textarea::make('ceneo_url')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ceneo_link_title'),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('humidification_efficiency')
                    ->numeric(),
                Forms\Components\TextInput::make('tested_efficiency')
                    ->numeric(),
                Forms\Components\TextInput::make('max_area')
                    ->numeric(),
                Forms\Components\TextInput::make('tested_max_area')
                    ->numeric(),
                Forms\Components\TextInput::make('water_tank_capacity')
                    ->numeric(),
                Forms\Components\TextInput::make('water_tank_min_time')
                    ->numeric(),
                Forms\Components\TextInput::make('water_tank_fill_type'),
                Forms\Components\Toggle::make('hygrostat')
                    ->required(),
                Forms\Components\TextInput::make('hygrostat_min')
                    ->numeric(),
                Forms\Components\TextInput::make('hygrostat_max')
                    ->numeric(),
                Forms\Components\TextInput::make('hygrostat_step')
                    ->numeric(),
                Forms\Components\TextInput::make('fan_modes_count')
                    ->numeric(),
                Forms\Components\TextInput::make('min_fan_volume')
                    ->numeric(),
                Forms\Components\TextInput::make('max_fan_volume')
                    ->numeric(),
                Forms\Components\Toggle::make('night_mode')
                    ->required(),
                Forms\Components\Toggle::make('evaporative_filter')
                    ->required(),
                Forms\Components\TextInput::make('evaporative_filter_life')
                    ->numeric(),
                Forms\Components\TextInput::make('evaporative_filter_price')
                    ->numeric(),
                Forms\Components\Toggle::make('silver_ion')
                    ->required(),
                Forms\Components\TextInput::make('silver_ion_life')
                    ->numeric(),
                Forms\Components\TextInput::make('silver_ion_price')
                    ->numeric(),
                Forms\Components\Toggle::make('ceramic_filter')
                    ->required(),
                Forms\Components\TextInput::make('ceramic_filter_life')
                    ->numeric(),
                Forms\Components\TextInput::make('ceramic_filter_price')
                    ->numeric(),
                Forms\Components\Toggle::make('uv_lamp')
                    ->required(),
                Forms\Components\Toggle::make('ionization')
                    ->required(),
                Forms\Components\Toggle::make('mobile_app')
                    ->required(),
                Forms\Components\Textarea::make('mobile_features')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('control_other')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('remote_control')
                    ->required(),
                Forms\Components\Textarea::make('functions')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('min_rated_power_consumption')
                    ->numeric(),
                Forms\Components\TextInput::make('max_rated_power_consumption')
                    ->numeric(),
                Forms\Components\TextInput::make('rated_voltage'),
                Forms\Components\TextInput::make('width')
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->numeric(),
                Forms\Components\TextInput::make('depth')
                    ->numeric(),
                Forms\Components\TextInput::make('weight')
                    ->numeric(),
                Forms\Components\TextInput::make('colors'),
                Forms\Components\TextInput::make('capability_points')
                    ->numeric(),
                Forms\Components\TextInput::make('capability')
                    ->numeric(),
                Forms\Components\TextInput::make('profitability_points')
                    ->numeric(),
                Forms\Components\TextInput::make('ranking')
                    ->numeric(),
                Forms\Components\TextInput::make('profitability')
                    ->numeric(),
                Forms\Components\Textarea::make('review_link')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('ranking_hidden')
                    ->required(),
                Forms\Components\TextInput::make('Filter_cots_humi')
                    ->numeric(),
                Forms\Components\Toggle::make('disks')
                    ->required(),
                Forms\Components\TextInput::make('main_ranking')
                    ->numeric(),
                Forms\Components\Toggle::make('for_plant')
                    ->required(),
                Forms\Components\Toggle::make('for_desk')
                    ->required(),
                Forms\Components\Toggle::make('alergic')
                    ->required(),
                Forms\Components\Toggle::make('astmatic')
                    ->required(),
                Forms\Components\Toggle::make('small')
                    ->required(),
                Forms\Components\Toggle::make('for_kids')
                    ->required(),
                Forms\Components\Toggle::make('big_area')
                    ->required(),
                Forms\Components\TextInput::make('humidification_area')
                    ->numeric(),
                Forms\Components\TextInput::make('max_area_ro')
                    ->numeric(),
                Forms\Components\TextInput::make('max_performance')
                    ->numeric(),
                Forms\Components\TextInput::make('hepa_filter_class'),
                Forms\Components\Toggle::make('mesh_filter')
                    ->required(),
                Forms\Components\Toggle::make('carbon_filter')
                    ->required(),
                Forms\Components\TextInput::make('type_of_device'),
                Forms\Components\Toggle::make('is_promo')
                    ->required(),
                Forms\Components\Textarea::make('gallery')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                ->importer(AirHumidifierImporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_created')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_created')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_updated')
                    ->numeric()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('partner_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_link_rel_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('partner_link_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ceneo_link_rel_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ceneo_link_title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('humidification_efficiency')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tested_efficiency')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_area')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tested_max_area')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('water_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('water_tank_min_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('water_tank_fill_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('hygrostat')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hygrostat_min')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hygrostat_max')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hygrostat_step')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fan_modes_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_fan_volume')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_fan_volume')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('night_mode')
                    ->boolean(),
                Tables\Columns\IconColumn::make('evaporative_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('evaporative_filter_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('evaporative_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('silver_ion')
                    ->boolean(),
                Tables\Columns\TextColumn::make('silver_ion_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('silver_ion_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ceramic_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ceramic_filter_life')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ceramic_filter_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('uv_lamp')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ionization')
                    ->boolean(),
                Tables\Columns\IconColumn::make('mobile_app')
                    ->boolean(),
                Tables\Columns\IconColumn::make('remote_control')
                    ->boolean(),
                Tables\Columns\TextColumn::make('min_rated_power_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_rated_power_consumption')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rated_voltage')
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
                Tables\Columns\TextColumn::make('colors')
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
                Tables\Columns\TextColumn::make('ranking')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profitability')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ranking_hidden')
                    ->boolean(),
                Tables\Columns\TextColumn::make('Filter_cots_humi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('disks')
                    ->boolean(),
                Tables\Columns\TextColumn::make('main_ranking')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('for_plant')
                    ->boolean(),
                Tables\Columns\IconColumn::make('for_desk')
                    ->boolean(),
                Tables\Columns\IconColumn::make('alergic')
                    ->boolean(),
                Tables\Columns\IconColumn::make('astmatic')
                    ->boolean(),
                Tables\Columns\IconColumn::make('small')
                    ->boolean(),
                Tables\Columns\IconColumn::make('for_kids')
                    ->boolean(),
                Tables\Columns\IconColumn::make('big_area')
                    ->boolean(),
                Tables\Columns\TextColumn::make('humidification_area')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_area_ro')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_performance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hepa_filter_class')
                    ->searchable(),
                Tables\Columns\IconColumn::make('mesh_filter')
                    ->boolean(),
                Tables\Columns\IconColumn::make('carbon_filter')
                    ->boolean(),
                Tables\Columns\TextColumn::make('type_of_device')
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListAirHumidifiers::route('/'),
            'create' => Pages\CreateAirHumidifier::route('/create'),
            'view' => Pages\ViewAirHumidifier::route('/{record}'),
            'edit' => Pages\EditAirHumidifier::route('/{record}/edit'),
        ];
    }
}
