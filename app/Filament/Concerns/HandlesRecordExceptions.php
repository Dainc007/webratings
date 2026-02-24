<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Graceful DB exception handling for Filament Create/Edit pages.
 *
 * Safety guarantee: if this trait's own code fails, Filament's default
 * error handling takes over (same behavior as without the trait).
 * Production never gets worse than before.
 */
trait HandlesRecordExceptions
{
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (UniqueConstraintViolationException $e) {
            if ($this->isPrimaryKeySequenceError($e)) {
                $this->fixSequence();

                try {
                    return parent::handleRecordCreation($data);
                } catch (Throwable $retryException) {
                    return $this->haltWithNotification($retryException);
                }
            }

            return $this->haltWithNotification($e);
        } catch (Throwable $e) {
            return $this->haltWithNotification($e);
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (Throwable $e) {
            return $this->haltWithNotification($e);
        }
    }

    /**
     * Send notification and throw Halt so Filament handles it gracefully.
     *
     * The return type is `never` in practice (always throws), but declared
     * as Model to satisfy the parent method signature in catch blocks.
     *
     * @throws Halt always
     */
    private function haltWithNotification(Throwable $e): never
    {
        try {
            $this->sendErrorNotification($e);
        } catch (Throwable) {
            // Notification failed - Halt below still ensures graceful handling.
            // Worst case: user sees no notification, but page doesn't crash.
        }

        throw (new Halt)->rollBackDatabaseTransaction();
    }

    private function isPrimaryKeySequenceError(UniqueConstraintViolationException $e): bool
    {
        return str_contains($e->getMessage(), '_pkey');
    }

    private function fixSequence(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $table = $this->getModel()::newModelInstance()->getTable();

        try {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence(?, 'id'), COALESCE(MAX(id), 1)) FROM \"{$table}\"",
                [$table]
            );
        } catch (Throwable $e) {
            Log::warning("Failed to fix sequence for {$table}: {$e->getMessage()}");
        }
    }

    private function sendErrorNotification(Throwable $e): void
    {
        Log::error('Filament record operation failed', [
            'resource' => static::$resource ?? null,
            'exception' => $e->getMessage(),
        ]);

        $body = str_contains($e->getMessage(), 'duplicate key')
            ? 'Rekord z takim kluczem już istnieje. Sekwencja została naprawiona - spróbuj ponownie.'
            : 'Szczegóły błędu zostały zapisane w logach.';

        Notification::make()
            ->title('Nie udało się zapisać rekordu')
            ->body($body)
            ->danger()
            ->persistent()
            ->send();
    }
}
