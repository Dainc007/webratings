<?php

declare(strict_types=1);

namespace App\Filament\Resources\FormLayoutItemResource\Pages;

use App\Filament\Resources\FormLayoutItemResource;
use Filament\Resources\Pages\ListRecords;

final class ListFormLayoutItems extends ListRecords
{
    protected static string $resource = FormLayoutItemResource::class;
}
