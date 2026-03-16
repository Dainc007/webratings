<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\LabelOverride;
use App\Models\User;
use App\Services\LabelService;
use Filament\Actions\Action;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Entry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $isProduction = $this->app->isProduction();

        Model::unguard();
        Model::shouldBeStrict($isProduction);
        Model::automaticallyEagerLoadRelationships();
        //        DB::prohibitDestructiveCommands($isProduction);
        URL::forceScheme('https');

        date_default_timezone_set(config('app.timezone'));

        if (! $isProduction) {
            Lang::handleMissingKeysUsing(function ($key, $replace, $locale, $fallback): void {
                Log::channel('translations')->info("Missing translation key: {$key} in locale: {$locale}");
            });
        }

        Gate::define('viewPulse', function (User $user): bool {
            return $user->isAdmin();
        });

        Vite::prefetch(concurrency: 3)->useAggressivePrefetching();

        $this->setDefaultFilamentSettings();
    }

    protected function setDefaultFilamentSettings(): void
    {
        Column::configureUsing(function (Column $column): void {
            $column
                ->wrapHeader()
                ->alignCenter()
                ->sortable()
                ->translateLabel();
        });
        Filter::configureUsing(function (Filter $filter): void {
            $filter->translateLabel();
        });
        Field::configureUsing(function (Field $field): void {
            $fieldName = $field->getName();

            if (str_starts_with($fieldName, '_lo_')) {
                return;
            }

            $field
                ->hintIcon('heroicon-o-information-circle', tooltip: $fieldName)
                ->extraAttributes(['data-db-column' => $fieldName])
                ->label(function () use ($field, $fieldName): ?string {
                    return self::resolveFieldLabel($field, $fieldName);
                })
                ->hintAction(
                    self::makeEditLabelAction($field, $fieldName)
                );
        });
        Entry::configureUsing(function (Entry $entry): void {
            $entry->translateLabel();
        });

        Action::configureUsing(function (Action $action): void {
            $action->translateLabel();
        });

        // not working in filament v4
        //        Component::configureUsing(function (Component $component): void {
        //            $component->translateLabel();
        //        });

        ImportColumn::configureUsing(function (ImportColumn $importColumn): void {
            $importColumn->requiredMapping();
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->recordUrl(null)
                ->recordActionsPosition(RecordActionsPosition::BeforeColumns);
        });
    }

    private static function getResourceTableName(Field $field): ?string
    {
        try {
            $livewire = $field->getLivewire();
            if ($livewire && method_exists($livewire, 'getResource')) {
                $resource = $livewire::getResource();
                return (new ($resource::getModel()))->getTable();
            }
        } catch (\Throwable) {
        }

        return null;
    }

    private static function resolveFieldLabel(Field $field, string $fieldName): ?string
    {
        $tableName = self::getResourceTableName($field);
        if ($tableName === null) {
            return null;
        }

        return LabelService::field($tableName, $fieldName);
    }

    private static function makeEditLabelAction(Field $field, string $fieldName): Action
    {
        return Action::make('editLabel')
            ->icon('heroicon-o-pencil-square')
            ->iconButton()
            ->tooltip('Edytuj etykietę')
            ->modalHeading(function () use ($field, $fieldName): string {
                $label = self::resolveFieldLabel($field, $fieldName);

                return 'Edytuj: ' . ($label ?? $fieldName);
            })
            ->size('xs')
            ->color('gray')
            ->form([
                TextInput::make('_lo_display_label')
                    ->label('Nowa nazwa')
                    ->placeholder('Pozostaw puste aby użyć domyślnej'),
            ])
            ->fillForm(function () use ($field, $fieldName): array {
                $tableName = self::getResourceTableName($field);
                if ($tableName === null) {
                    return [];
                }

                $override = LabelOverride::where([
                    'table_name' => $tableName,
                    'element_type' => 'field',
                    'element_key' => $fieldName,
                ])->first();

                return ['_lo_display_label' => $override?->display_label];
            })
            ->action(function (array $data, $livewire) use ($field, $fieldName): void {
                $tableName = self::getResourceTableName($field);
                if ($tableName === null) {
                    return;
                }

                $label = $data['_lo_display_label'] ?? null;

                if (empty($label)) {
                    LabelOverride::where([
                        'table_name' => $tableName,
                        'element_type' => 'field',
                        'element_key' => $fieldName,
                    ])->delete();
                } else {
                    LabelOverride::updateOrCreate(
                        [
                            'table_name' => $tableName,
                            'element_type' => 'field',
                            'element_key' => $fieldName,
                        ],
                        ['display_label' => $label]
                    );
                }

                LabelService::clearCache();

                Notification::make()
                    ->title('Etykieta zaktualizowana')
                    ->success()
                    ->send();

                $livewire->js('setTimeout(() => location.reload(), 500)');
            })
            ->visible(function () use ($field): bool {
                return self::getResourceTableName($field) !== null;
            });
    }
}
