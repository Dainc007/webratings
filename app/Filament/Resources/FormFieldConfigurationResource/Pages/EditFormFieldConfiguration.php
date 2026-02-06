<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormFieldConfigurationResource\Pages;

use App\Filament\Resources\FormFieldConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditFormFieldConfiguration extends EditRecord
{
    protected static string $resource = FormFieldConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
