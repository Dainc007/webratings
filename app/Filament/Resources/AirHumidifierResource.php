<?php

namespace App\Filament\Resources;

use App\Filament\Imports\AirHumidifierImporter;
use App\Filament\Resources\AirHumidifierResource\Pages;
use App\Filament\Resources\AirHumidifierResource\RelationManagers;
use App\Models\AirHumidifier;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
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
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('remote_id')
                                            ->label('Remote ID')
                                            ->required(),
                                        Forms\Components\TextInput::make('status')
                                            ->required(),
                                        Forms\Components\TextInput::make('model')
                                            ->required(),
                                        Forms\Components\TextInput::make('brand_name')
                                            ->required(),
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('partner_link_url')
                                            ->url()
                                            ->required(),
                                        Forms\Components\TextInput::make('ceneo_url')
                                            ->url()
                                            ->required(),
                                        Forms\Components\TextInput::make('review_link')
                                            ->url()
                                            ->required(),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Performance')
                            ->schema([
                                Forms\Components\Section::make('Performance')
                                    ->schema([
                                        Forms\Components\TextInput::make('max_performance')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('max_area')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('max_area_ro')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('humidification_efficiency')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('tested_efficiency')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('humidification_area')
                                            ->numeric(),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Water Tank')
                            ->schema([
                                Forms\Components\Section::make('Water Tank')
                                    ->schema([
                                        Forms\Components\TextInput::make('water_tank_capacity')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('water_tank_min_time')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('water_tank_fill_type'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Controls')
                            ->schema([
                                Forms\Components\Section::make('Controls')
                                    ->schema([
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
                                        Forms\Components\TextInput::make('control_other'),
                                        Forms\Components\Toggle::make('remote_control')
                                            ->required(),
                                        Forms\Components\TextInput::make('functions'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Filters')
                            ->schema([
                                Forms\Components\Section::make('Filters')
                                    ->schema([
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
                                        Forms\Components\TextInput::make('hepa_filter_class'),
                                        Forms\Components\Toggle::make('mesh_filter')
                                            ->required(),
                                        Forms\Components\Toggle::make('carbon_filter')
                                            ->required(),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Smart Features')
                            ->schema([
                                Forms\Components\Section::make('Smart Features')
                                    ->schema([
                                        Forms\Components\Toggle::make('mobile_app')
                                            ->required(),
                                        Forms\Components\TextInput::make('mobile_features'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Power & Dimensions')
                            ->schema([
                                Forms\Components\Section::make('Power & Dimensions')
                                    ->schema([
                                        Forms\Components\TextInput::make('min_rated_power_consumption')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('max_rated_power_consumption')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('rated_voltage')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('width')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('height')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('weight')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('depth')
                                            ->numeric(),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Categories')
                            ->schema([
                                Forms\Components\Section::make('Categories')
                                    ->schema([
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
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Ranking')
                            ->schema([
                                Forms\Components\Section::make('Ranking')
                                    ->schema([
                                        Forms\Components\TextInput::make('capability_points')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('capability'),
                                        Forms\Components\TextInput::make('profitability_points')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('ranking'),
                                        Forms\Components\TextInput::make('profitability'),
                                        Forms\Components\Toggle::make('ranking_hidden')
                                            ->required(),
                                        Forms\Components\TextInput::make('main_ranking'),
                                    ])->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Additional')
                            ->schema([
                                Forms\Components\Section::make('Additional')
                                    ->schema([
                                        Forms\Components\TextInput::make('colors'),
                                        Forms\Components\TextInput::make('type_of_device'),
                                        Forms\Components\Toggle::make('is_promo')
                                            ->required(),
                                        Forms\Components\TextInput::make('gallery'),
                                        Forms\Components\TextInput::make('Filter_cots_humi'),
                                        Forms\Components\Toggle::make('disks')
                                            ->required(),
                                    ])->columns(2),
                            ]),
                    ])
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
                Tables\Columns\TextColumn::make('remote_id')
                    ->label('Remote ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand_name')
                    ->searchable(),
                TextInputColumn::make('price')
                    ->width('50px')
                    ->extraInputAttributes(['step' => '0.01'])
                    ->afterStateUpdated(function ($record, $state): void {
                        Notification::make()
                            ->title('Cena została zaktualizowana')
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('max_performance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_area')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('water_tank_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filtersFormColumns(3)
            ->filters([
                Tables\Filters\SelectFilter::make('brand_name')
                    ->label('Brand')
                    ->options(fn () => AirHumidifier::query()
                        ->distinct()
                        ->pluck('brand_name', 'brand_name')
                        ->toArray()
                    ),
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_from')
                                    ->numeric()
                                    ->label('Price from'),
                                Forms\Components\TextInput::make('price_to')
                                    ->numeric()
                                    ->label('Price to'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn ($query, $price) => $query->where('price', '>=', $price)
                            )
                            ->when(
                                $data['price_to'],
                                fn ($query, $price) => $query->where('price', '<=', $price)
                            );
                    }),
                Tables\Filters\Filter::make('performance_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('performance_from')
                                    ->numeric()
                                    ->label('Performance from'),
                                Forms\Components\TextInput::make('performance_to')
                                    ->numeric()
                                    ->label('Performance to'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['performance_from'],
                                fn ($query, $performance) => $query->where('max_performance', '>=', $performance)
                            )
                            ->when(
                                $data['performance_to'],
                                fn ($query, $performance) => $query->where('max_performance', '<=', $performance)
                            );
                    }),
                Tables\Filters\Filter::make('area_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('area_from')
                                    ->numeric()
                                    ->label('Area from'),
                                Forms\Components\TextInput::make('area_to')
                                    ->numeric()
                                    ->label('Area to'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['area_from'],
                                fn ($query, $area) => $query->where('max_area', '>=', $area)
                            )
                            ->when(
                                $data['area_to'],
                                fn ($query, $area) => $query->where('max_area', '<=', $area)
                            );
                    }),
                Tables\Filters\Filter::make('water_tank_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('tank_from')
                                    ->numeric()
                                    ->label('Tank capacity from'),
                                Forms\Components\TextInput::make('tank_to')
                                    ->numeric()
                                    ->label('Tank capacity to'),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['tank_from'],
                                fn ($query, $capacity) => $query->where('water_tank_capacity', '>=', $capacity)
                            )
                            ->when(
                                $data['tank_to'],
                                fn ($query, $capacity) => $query->where('water_tank_capacity', '<=', $capacity)
                            );
                    }),
                Tables\Filters\TernaryFilter::make('hygrostat')
                    ->label('Hygrostat'),
                Tables\Filters\TernaryFilter::make('night_mode')
                    ->label('Night Mode'),
                Tables\Filters\TernaryFilter::make('mobile_app')
                    ->label('Mobile App'),
                Tables\Filters\TernaryFilter::make('remote_control')
                    ->label('Remote Control'),
                Tables\Filters\TernaryFilter::make('for_plant')
                    ->label('For Plants'),
                Tables\Filters\TernaryFilter::make('for_desk')
                    ->label('For Desk'),
                Tables\Filters\TernaryFilter::make('alergic')
                    ->label('For Allergies'),
                Tables\Filters\TernaryFilter::make('astmatic')
                    ->label('For Asthma'),
                Tables\Filters\TernaryFilter::make('for_kids')
                    ->label('For Kids'),
                Tables\Filters\TernaryFilter::make('small')
                    ->label('Small Size'),
                Tables\Filters\TernaryFilter::make('big_area')
                    ->label('Big Area'),
            ], layout: \Filament\Tables\Enums\FiltersLayout::Modal)
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
