<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormTabConfigurationResource\Pages;

use App\Filament\Resources\FormTabConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditFormTabConfiguration extends EditRecord
{
    protected static string $resource = FormTabConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
