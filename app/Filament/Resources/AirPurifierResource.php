<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Imports\AirPurifierImporter;
use App\Filament\Resources\AirPurifierResource\Pages;
use App\Models\AirPurifier;
use App\Models\CustomField;
use App\Models\TableColumnPreference;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class AirPurifierResource extends Resource
{
    protected static ?string $model = AirPurifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Oczyszczacze Powietrza';

    protected static ?string $pluralLabel = 'Oczyszczacze Powietrza';

    protected static ?string $label = 'Oczyszczacze Powietrza';

    protected static ?string $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $customFields = CustomField::where('table_name', 'air_purifiers')->get();
        $customFieldSchema = [];
        foreach ($customFields as $customField) {
            if ($customField->column_type === 'boolean') {
                $field = Toggle::make($customField->column_name);
            } else {
                $field = TextInput::make($customField->column_name);
            }

            if ($customField->column_type === 'integer') {
                $field->numeric();
            }
            $customFieldSchema[] = $field->label($customField->display_name);
        }

        return $form
            ->schema([
                Tabs::make('Air Purifier Form')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending' => 'Pending',
                                        'published' => 'Published',
                                    ])
                                    ->required(),

                                TextInput::make('model')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('brand_name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('PLN'),

                                DateTimePicker::make('price_date'),

                                Toggle::make('is_promo')
                                    ->label('Promotional Item'),

                                Section::make('Links')
                                    ->schema([
                                        TextInput::make('partner_link_url')
                                            ->url()
                                            ->maxLength(255),

                                        TagsInput::make('partner_link_rel_2')
                                            ->placeholder('Add relation')
                                            ->separator(',')
                                            ->helperText('e.g., nofollow, noopener'),

                                        TextInput::make('ceneo_url')
                                            ->url()
                                            ->maxLength(255),

                                        TagsInput::make('ceneo_link_rel_2')
                                            ->placeholder('Add relation')
                                            ->separator(',')
                                            ->helperText('e.g., nofollow, noopener'),

                                        TextInput::make('review_link')
                                            ->url()
                                            ->maxLength(255),
                                    ])->collapsible(),
                            ]),

                        Tabs\Tab::make('Performance')
                            ->schema([
                                TextInput::make('max_performance')
                                    ->label('Maximum Performance (m³/h)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_area')
                                    ->label('Maximum Area (m²)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_area_ro')
                                    ->label('Maximum Area RO (m²)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('min_loudness')
                                    ->label('Minimum Loudness (dB)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_loudness')
                                    ->label('Maximum Loudness (dB)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_rated_power_consumption')
                                    ->label('Maximum Power Consumption (W)')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('capability_points')
                                    ->label('Capability Points')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('profitability_points')
                                    ->label('Profitability Points')
                                    ->numeric()
                                    ->nullable(),
                            ]),

                        Tabs\Tab::make('Humidification')
                            ->schema([
                                Toggle::make('has_humidification')
                                    ->label('Has Humidification'),

                                Select::make('humidification_type')
                                    ->options([
                                        'vapor' => 'Vapor',
                                        'ultrasonic' => 'Ultrasonic',
                                        'evaporative' => 'Evaporative',
                                    ])
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                Toggle::make('humidification_switch')
                                    ->label('Humidification Switch')
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                TextInput::make('humidification_efficiency')
                                    ->label('Humidification Efficiency (ml/h)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                TextInput::make('humidification_area')
                                    ->label('Humidification Area (m²)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable()
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                TextInput::make('water_tank_capacity')
                                    ->label('Water Tank Capacity (ml)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                Toggle::make('hygrometer')
                                    ->label('Has Hygrometer'),

                                Toggle::make('hygrostat')
                                    ->label('Has Hygrostat'),
                            ]),

                        Tabs\Tab::make('Filters')
                            ->schema([
                                Section::make('Evaporative Filter')
                                    ->schema([
                                        Toggle::make('evaporative_filter')
                                            ->label('Has Evaporative Filter'),

                                        TextInput::make('evaporative_filter_life')
                                            ->label('Filter Life (months)')
                                            ->numeric()
                                            ->visible(fn(callable $get) => $get('evaporative_filter')),

                                        TextInput::make('evaporative_filter_price')
                                            ->label('Filter Price (PLN)')
                                            ->numeric()
                                            ->visible(fn(callable $get) => $get('evaporative_filter')),
                                    ])->collapsible(),

                                Section::make('HEPA Filter')
                                    ->schema([
                                        Toggle::make('hepa_filter')
                                            ->label('Has HEPA Filter'),

                                        TextInput::make('hepa_filter_class')
                                            ->label('HEPA Filter Class')
                                            ->visible(fn(callable $get) => $get('hepa_filter')),

                                        TextInput::make('effectiveness_hepa_filter')
                                            ->label('HEPA Filter Effectiveness (%)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->visible(fn(callable $get) => $get('hepa_filter')),

                                        TextInput::make('hepa_filter_service_life')
                                            ->label('Filter Life (months)')
                                            ->numeric()
                                            ->visible(fn(callable $get) => $get('hepa_filter')),

                                        TextInput::make('hepa_filter_price')
                                            ->label('Filter Price (PLN)')
                                            ->numeric()
                                            ->visible(fn(callable $get) => $get('hepa_filter')),
                                    ])->collapsible(),

                                Section::make('Carbon Filter')
                                    ->schema([
                                        Toggle::make('carbon_filter')
                                            ->label('Has Carbon Filter'),

                                        TextInput::make('carbon_filter_service_life')
                                            ->label('Filter Life (months)')
                                            ->numeric()
                                            ->visible(fn(callable $get) => $get('carbon_filter')),

                                        TextInput::make('carbon_filter_price')
                                            ->label('Filter Price (PLN)')
                                            ->numeric()
                                            ->visible(fn(callable $get) => $get('carbon_filter')),
                                    ])->collapsible(),

                                Toggle::make('mesh_filter')
                                    ->label('Has Mesh Filter'),

                                Textarea::make('filter_costs')
                                    ->label('Filter Costs Summary'),
                            ]),

                        Tabs\Tab::make('Features')
                            ->schema([
                                Section::make('Ionizer')
                                    ->schema([
                                        Toggle::make('ionization')
                                            ->label('Has Ionization'),

                                        TextInput::make('ionizer_type')
                                            ->label('Ionizer Type')
                                            ->visible(fn(callable $get) => $get('ionization')),

                                        Toggle::make('ionizer_switch')
                                            ->label('Ionizer Switch')
                                            ->visible(fn(callable $get) => $get('ionization')),
                                    ])->collapsible(),

                                Section::make('Other Features')
                                    ->schema([
                                        Toggle::make('uvc')
                                            ->label('Has UVC'),

                                        Toggle::make('mobile_app')
                                            ->label('Has Mobile App'),

                                        Toggle::make('remote_control')
                                            ->label('Has Remote Control'),

                                        TagsInput::make('functions_and_equipment')
                                            ->placeholder('Add function')
                                            ->separator(','),

                                        Toggle::make('heating_and_cooling_function')
                                            ->label('Has Heating and Cooling'),

                                        Toggle::make('cooling_function')
                                            ->label('Has Cooling Function'),
                                    ])->collapsible(),

                                Section::make('Sensors')
                                    ->schema([
                                        Toggle::make('pm2_sensor')
                                            ->label('Has PM2.5 Sensor'),

                                        Toggle::make('lzo_tvcop_sensor')
                                            ->label('Has LZO/TVCOP Sensor'),

                                        Toggle::make('temperature_sensor')
                                            ->label('Has Temperature Sensor'),

                                        Toggle::make('humidity_sensor')
                                            ->label('Has Humidity Sensor'),

                                        Toggle::make('light_sensor')
                                            ->label('Has Light Sensor'),
                                    ])->collapsible(),

                                TagsInput::make('certificates')
                                    ->placeholder('Add certificate')
                                    ->separator(','),
                            ]),

                        Tabs\Tab::make('Physical Attributes')
                            ->schema([
                                TextInput::make('width')
                                    ->label('Width (cm)')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('height')
                                    ->label('Height (cm)')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('depth')
                                    ->label('Depth (cm)')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('weight')
                                    ->label('Weight (kg)')
                                    ->numeric()
                                    ->step(0.1),

                                TagsInput::make('colors')
                                    ->placeholder('Add color')
                                    ->separator(','),
                            ]),

                        Tabs\Tab::make('Classification')
                            ->schema([
                                TagsInput::make('type_of_device')
                                    ->placeholder('Add device type')
                                    ->separator(','),

                                TextInput::make('type')
                                    ->numeric(),

                                Toggle::make('main_ranking')
                                    ->label('Include in Main Ranking'),

                                Toggle::make('ranking_hidden')
                                    ->label('Hide from Rankings'),

                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('for_kids')
                                            ->label('Suitable for Kids'),

                                        Toggle::make('bedroom')
                                            ->label('Suitable for Bedroom'),

                                        Toggle::make('smokers')
                                            ->label('Suitable for Smokers'),

                                        Toggle::make('office')
                                            ->label('Suitable for Office'),

                                        Toggle::make('kindergarten')
                                            ->label('Suitable for Kindergarten'),

                                        Toggle::make('astmatic')
                                            ->label('Suitable for Asthmatics'),

                                        Toggle::make('alergic')
                                            ->label('Suitable for Allergies'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Timestamps')
                            ->schema([
                                DateTimePicker::make('date_created')
                                    ->label('Created Date')
                                    ->disabled(),

                                DateTimePicker::make('date_updated')
                                    ->label('Updated Date')
                                    ->disabled(),

                                DateTimePicker::make('created_at')
                                    ->label('Record Created At')
                                    ->disabled(),

                                DateTimePicker::make('updated_at')
                                    ->label('Record Updated At')
                                    ->disabled(),
                            ]),

                        Tabs\Tab::make('Custom Fields')
                            ->schema(
                                $customFieldSchema
                            ),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $availableColumns = [
            TextColumn::make('id')->label('id')->hidden(),
        ];

        $columns = TableColumnPreference::where('table_name', 'air_purifiers')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($columns as $column) {
            $field = TextColumn::make($column['column_name'])->label(
                __($column['table_name'] . '.' . $column['column_name'])
            );
            $field->sortable()->searchable()->alignCenter();

            if ($column['column_name'] === 'price') {
                $field = TextInputColumn::make($column['column_name'])
                    ->label(
                        __($column['table_name'] . '.' . $column['column_name'])
                    )
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->width('50px')
                    ->extraInputAttributes(['step' => '0.01'])
                    ->afterStateUpdated(function ($record, $state): void {
                        Notification::make()
                            ->title('Cena została zaktualizowana')
                            ->success()
                            ->send();
                    });
            }

            $availableColumns[] = $field;
        }

        $customFields = CustomField::where('table_name', 'air_purifiers')->get();
        foreach ($customFields as $customField) {
            if ($customField->column_type === 'boolean') {
                $field = ToggleColumn::make($customField->column_name);
            } else {
                $field = TextColumn::make($customField->column_name);
            }

            if ($customField->column_type === 'integer') {
                $field->numeric();
            }

            $field->sortable()->searchable()->label($customField->display_name);

            $availableColumns[] = $field;
        }

        return $table
            ->recordUrl(null)
            ->heading(
                fn(): string => 'Łącznie Produktów: ' . AirPurifier::count()
            )
            ->columns($availableColumns)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make('Import Products')
                    ->label('Importuj')
                    ->importer(AirPurifierImporter::class),
                Tables\Actions\Action::make('column_settings')
                    ->label('')->label('Ustawienia')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn() => route('filament.admin.resources.table-column-preferences.index', [
                        'tableFilters' => [
                            'table_name' => [
                                'value' => 'air_purifiers',
                            ],
                        ],
                    ])),
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
            'index' => Pages\ListAirPurifiers::route('/'),
            'create' => Pages\CreateAirPurifier::route('/create'),
            'edit' => Pages\EditAirPurifier::route('/{record}/edit'),
        ];
    }
}
