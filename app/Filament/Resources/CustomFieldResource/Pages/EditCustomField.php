<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Enums\CustomFieldStatus;
use App\Filament\Resources\CustomFieldResource;
use App\Services\CustomFieldService;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

final class EditCustomField extends EditRecord
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete')
                ->label('Usuń')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->visible(fn () => $this->getRecord()->status === CustomFieldStatus::ACTIVE)
                ->action(function (): void {
                    CustomFieldService::deleteField($this->getRecord());

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
