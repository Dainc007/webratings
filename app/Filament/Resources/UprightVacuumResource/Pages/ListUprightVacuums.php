<?php

declare(strict_types=1);

namespace App\Filament\Resources\UprightVacuumResource\Pages;

use App\Filament\Resources\UprightVacuumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListUprightVacuums extends ListRecords
{
    protected static string $resource = UprightVacuumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
