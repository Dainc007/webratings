<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorResource\Pages;
use App\Filament\Resources\SensorResource\RelationManagers;
use App\Models\Sensor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SensorResource\Importers\SensorImporter;

class SensorResource extends Resource
{
    protected static ?string $model = Sensor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('price_before')
                    ->numeric(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\Textarea::make('discount_info')
                    ->columnSpanFull(),
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
                Forms\Components\Toggle::make('is_pm1'),
                Forms\Components\TextInput::make('pm1_range'),
                Forms\Components\TextInput::make('pm1_accuracy'),
                Forms\Components\TextInput::make('pm1_sensor_type'),
                Forms\Components\Toggle::make('is_pm2'),
                Forms\Components\TextInput::make('pm2_range'),
                Forms\Components\TextInput::make('pm2_accuracy'),
                Forms\Components\TextInput::make('pm2_sensor_type'),
                Forms\Components\Toggle::make('is_pm10'),
                Forms\Components\TextInput::make('pm10_range'),
                Forms\Components\TextInput::make('pm10_accuracy'),
                Forms\Components\TextInput::make('pm10_sensor_type'),
                Forms\Components\Toggle::make('is_lzo'),
                Forms\Components\TextInput::make('lzo_range'),
                Forms\Components\TextInput::make('lzo_accuracy'),
                Forms\Components\TextInput::make('lzo_sensor_type'),
                Forms\Components\Toggle::make('is_hcho'),
                Forms\Components\TextInput::make('hcho_range'),
                Forms\Components\TextInput::make('hcho_accuracy'),
                Forms\Components\TextInput::make('hcho_sensor_type'),
                Forms\Components\Toggle::make('is_co2'),
                Forms\Components\TextInput::make('co2_range'),
                Forms\Components\TextInput::make('co2_accuracy'),
                Forms\Components\TextInput::make('co2_sensor_type'),
                Forms\Components\Toggle::make('is_co'),
                Forms\Components\TextInput::make('co_range'),
                Forms\Components\TextInput::make('co_accuracy'),
                Forms\Components\TextInput::make('co_sensor_type'),
                Forms\Components\Toggle::make('is_temperature'),
                Forms\Components\TextInput::make('temperature_range'),
                Forms\Components\TextInput::make('temperature_accuracy'),
                Forms\Components\Toggle::make('is_humidity'),
                Forms\Components\TextInput::make('humidity_range'),
                Forms\Components\TextInput::make('humidity_accuracy'),
                Forms\Components\Toggle::make('is_pressure'),
                Forms\Components\TextInput::make('pressure_range'),
                Forms\Components\TextInput::make('pressure_accuracy'),
                Forms\Components\TextInput::make('battery'),
                Forms\Components\TextInput::make('battery_capacity')
                    ->numeric(),
                Forms\Components\TextInput::make('voltage')
                    ->numeric(),
                Forms\Components\Toggle::make('has_power_cord'),
                Forms\Components\Toggle::make('wifi'),
                Forms\Components\Textarea::make('mobile_features')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('bluetooth'),
                Forms\Components\Toggle::make('has_history'),
                Forms\Components\Toggle::make('has_display'),
                Forms\Components\Toggle::make('has_alarm'),
                Forms\Components\Toggle::make('has_assessment'),
                Forms\Components\Toggle::make('has_outdoor_indicator'),
                Forms\Components\Toggle::make('has_battery_indicator'),
                Forms\Components\Toggle::make('has_clock'),
                Forms\Components\TextInput::make('temperature'),
                Forms\Components\TextInput::make('humidity'),
                Forms\Components\TextInput::make('width')
                    ->numeric(),
                Forms\Components\TextInput::make('height')
                    ->numeric(),
                Forms\Components\TextInput::make('depth')
                    ->numeric(),
                Forms\Components\TextInput::make('weight')
                    ->numeric(),
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
                Forms\Components\TextInput::make('main_ranking'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->headerActions([
            Tables\Actions\ImportAction::make('Import Sensors')
                ->importer(SensorImporter::class),
            Tables\Actions\Action::make('Ustawienia')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                    'tableFilters' => [
                        'table_name' => [
                            'value' => 'sensors',
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
                Tables\Columns\IconColumn::make('is_pm1')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pm1_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm1_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm1_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_pm2')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pm2_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm2_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm2_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_pm10')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pm10_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm10_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm10_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_lzo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('lzo_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lzo_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lzo_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_hcho')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hcho_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hcho_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hcho_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_co2')
                    ->boolean(),
                Tables\Columns\TextColumn::make('co2_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co2_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co2_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_co')
                    ->boolean(),
                Tables\Columns\TextColumn::make('co_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('co_sensor_type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_temperature')
                    ->boolean(),
                Tables\Columns\TextColumn::make('temperature_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('temperature_accuracy')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_humidity')
                    ->boolean(),
                Tables\Columns\TextColumn::make('humidity_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('humidity_accuracy')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_pressure')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pressure_range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pressure_accuracy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('battery')
                    ->searchable(),
                Tables\Columns\TextColumn::make('battery_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_power_cord')
                    ->boolean(),
                Tables\Columns\IconColumn::make('wifi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('bluetooth')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_history')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_display')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_alarm')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_assessment')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_outdoor_indicator')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_battery_indicator')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_clock')
                    ->boolean(),
                Tables\Columns\TextColumn::make('temperature')
                    ->searchable(),
                Tables\Columns\TextColumn::make('humidity')
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
            'index' => Pages\ListSensors::route('/'),
            'create' => Pages\CreateSensor::route('/create'),
            'edit' => Pages\EditSensor::route('/{record}/edit'),
        ];
    }
}
