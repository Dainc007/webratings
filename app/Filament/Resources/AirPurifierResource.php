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
use Illuminate\Support\Facades\Schema;

final class AirPurifierResource extends Resource
{
    protected static ?string $model = AirPurifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Oczyszczacze Powietrza';

    protected static ?string $pluralLabel = 'Oczyszczacze Powietrza';

    protected static ?string $label = 'Oczyszczacze Powietrza';

    protected static ?string $navigationGroup = 'Produkty';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'model';


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
            $customFieldSchema[] = $field;
        }

        return $form
            ->schema([
                Tabs::make('Air Purifier Form')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Select::make('status')
                                    ->selectablePlaceholder(false)
                                    ->options([
                                        'draft' => 'Szkic',
                                        'published' => 'Opublikowany',
                                        'archived' => "Zarchiwizowany"
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

                                DateTimePicker::make('price_date')->default(now()),

                                Toggle::make('is_promo'),

                                Section::make('Links')
                                    ->schema([
                                        TextInput::make('partner_link_url')
                                            ->maxLength(255),

                                        Select::make('ceneo_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                                ]),

                                        TextInput::make('ceneo_url')
                                            ->maxLength(255),

                                        Select::make('partner_link_rel_2')
                                            ->multiple()
                                            ->options([
                                                'nofollow' => 'nofollow',
                                                'dofollow' => 'dofollow',
                                                'sponsored' => 'sponsored',
                                                'noopener' => 'noopener',
                                            ]),

                                        TextInput::make('review_link')
                                            ->maxLength(255),
                                    ])->collapsible(),
                            ]),

                        Tabs\Tab::make('Performance')
                        ->columns(4)
                            ->schema([
                                TextInput::make('max_performance')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_area')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_area_ro')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('min_loudness')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_loudness')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_rated_power_consumption')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('capability_points')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('profitability_points')
                                    ->numeric()
                                    ->nullable(),
                            ])
                            ,

                        Tabs\Tab::make('Humidification')
                        ->columns(4)
                            ->schema([
                                Toggle::make('has_humidification'),

                                Select::make('humidification_type')
                                    ->options([
                                        'vapor' => 'Vapor',
                                        'ultrasonic' => 'Ultrasonic',
                                        'evaporative' => 'Evaporative',
                                    ])
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                Toggle::make('humidification_switch')
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                TextInput::make('humidification_efficiency')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                TextInput::make('humidification_area')
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable()
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                TextInput::make('water_tank_capacity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn(callable $get) => $get('has_humidification')),

                                Toggle::make('hygrometer'),

                                Toggle::make('hygrostat'),
                            ]),

                        Tabs\Tab::make('Filters')
                            ->schema([
                                Toggle::make('evaporative_filter')->live(),
                                Section::make('Filtr ewaporacyjny')
                                    ->schema([
                                        TextInput::make('evaporative_filter_life')
                                            ->numeric(),
                                        TextInput::make('evaporative_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn(callable $get) => $get('evaporative_filter')),

                                Toggle::make('hepa_filter')->live(),
                                Section::make('Filtr HEPA')
                                    ->schema([
                                        TextInput::make('hepa_filter_class'),
                                        TextInput::make('effectiveness_hepa_filter')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100),
                                        TextInput::make('hepa_filter_service_life')
                                            ->numeric(),
                                        TextInput::make('hepa_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn(callable $get) => $get('hepa_filter')),

                                Toggle::make('carbon_filter')->live(),
                                Section::make('Filtr węglowy')
                                    ->schema([
                                        TextInput::make('carbon_filter_service_life')
                                            ->numeric(),
                                        TextInput::make('carbon_filter_price')
                                            ->numeric(),
                                    ])
                                    ->collapsible()
                                    ->visible(fn(callable $get) => $get('carbon_filter')),

                                Toggle::make('mesh_filter'),

                                Textarea::make('filter_costs'),
                            ]),

                        Tabs\Tab::make('Features')
                            ->schema([
                                Toggle::make('ionization')->live(),
                                Section::make('Ionizer')
                                    ->schema([
                                        TextInput::make('ionizer_type'),
                                        Toggle::make('ionizer_switch'),
                                    ])
                                    ->visible(fn(callable $get) => $get('ionization')),

                                Section::make('Other Features')
                                    ->schema([
                                        Toggle::make('uvc'),

                                        Toggle::make('mobile_app'),

                                        Toggle::make('remote_control'),

                                        TagsInput::make('functions_and_equipment')
                                            ->placeholder('Add function')
                                            ->separator(','),

                                        Toggle::make('heating_and_cooling_function'),

                                        Toggle::make('cooling_function'),
                                    ])->collapsible(),

                                Section::make('Sensors')
                                    ->schema([
                                        Toggle::make('pm2_sensor'),

                                        Toggle::make('lzo_tvcop_sensor'),

                                        Toggle::make('temperature_sensor'),

                                        Toggle::make('humidity_sensor'),

                                        Toggle::make('light_sensor'),
                                    ])->collapsible(),

                                TagsInput::make('certificates')
                                    ->placeholder('Add certificate')
                                    ->separator(','),
                            ]),

                        Tabs\Tab::make('Physical Attributes')
                        ->columns(4)
                            ->schema([
                                TextInput::make('width')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('height')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('depth')
                                    ->numeric()
                                    ->nullable(),

                                TextInput::make('weight')
                                    ->numeric()
                                    ->step(0.1),

                                TagsInput::make('colors')
                                    ->columnSpanFull()
                                    ->placeholder('Dodaj kolor')
                                    ->separator(','),
                            ]),

                        Tabs\Tab::make('Classification')
                            ->schema([
                                TagsInput::make('type_of_device')
                                    ->placeholder('Add device type')
                                    ->separator(','),

                                Toggle::make('main_ranking'),

                                Toggle::make('ranking_hidden'),

                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('for_kids'),

                                        Toggle::make('bedroom'),

                                        Toggle::make('smokers'),

                                        Toggle::make('office'),

                                        Toggle::make('kindergarten'),

                                        Toggle::make('astmatic'),

                                        Toggle::make('alergic'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Timestamps')
                            ->schema([
                                DateTimePicker::make('date_created')
                                    ->disabled(),

                                DateTimePicker::make('date_updated')
                                    ->disabled(),

                                DateTimePicker::make('created_at')
                                    ->disabled(),

                                DateTimePicker::make('updated_at')
                                    ->disabled(),
                            ]),

                        Tabs\Tab::make('custom_fields')
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
            TextColumn::make('id')->hidden(),
        ];

        $columns = TableColumnPreference::where('table_name', 'air_purifiers')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($columns as $column) {
            $field = TextColumn::make($column['column_name']);

            if ($column['column_name'] === 'price') {
                $field = TextInputColumn::make($column['column_name'])
                    ->width('50px')
                    ->extraInputAttributes(['step' => '0.01'])
                    ->afterStateUpdated(function ($record, $state): void {
                        Notification::make()
                            ->title('Cena została zaktualizowana')
                            ->success()
                            ->send();
                    });
            }

            $field->when(
                Schema::hasColumn('air_purifiers', $column['column_name']),
                fn () => $field->searchable()
            );

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

            $field->searchable();

            $field->when(
                Schema::hasColumn('air_purifiers', $customField->column_name),
                fn () => $field->searchable()
            );

            $label = $customField->display_name ?? __($customField->column_name);
            $field->label($label);

            $availableColumns[] = $field;
        }

        return $table
            ->recordUrl(null)
            ->columns($availableColumns)
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make('Import Products')
                    ->importer(AirPurifierImporter::class),
                Tables\Actions\Action::make('Ustawienia')
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['brand_name', 'model'];
    }
}
