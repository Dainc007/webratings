<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormSectionConfigurationResource\Pages;

use App\Filament\Resources\FormSectionConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditFormSectionConfiguration extends EditRecord
{
    protected static string $resource = FormSectionConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
