<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Services\LabelService;
use Filament\Actions\Action;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

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
                ->alignCenter()
                ->sortable()
                ->translateLabel();
        });
        Filter::configureUsing(function (Filter $filter): void {
            $filter->translateLabel();
        });
        Field::configureUsing(function (Field $field): void {
            $fieldName = $field->getName();

            $field
                ->hintIcon(function () use ($field): ?string {
                    return self::getResourceTableName($field) !== null
                        ? 'heroicon-o-information-circle'
                        : null;
                }, tooltip: $fieldName)
                ->label(function () use ($field, $fieldName): ?string {
                    return self::resolveFieldLabel($field, $fieldName);
                });
        });
        Entry::configureUsing(function (Entry $entry): void {
            $entry->translateLabel();
        });

        Action::configureUsing(function (Action $action): void {
            $action->translateLabel();
        });

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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AppServiceProvider::getResourceTableName failed', [
                'exception' => $e->getMessage(),
            ]);
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
}
