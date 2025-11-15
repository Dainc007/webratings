<?php

declare(strict_types=1);

namespace App\Services;

use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;

final class ExportActionService
{
    /**
     * Create an ExportAction for header (exports all records)
     */
    public static function createExportAllAction(string $tableName, ?string $label = null, ?string $icon = null): ExportAction
    {
        $label = $label ?? 'Eksportuj wszystko';
        $icon = $icon ?? 'heroicon-o-arrow-down-tray';
        
        return ExportAction::make('export_all')
            ->label($label)
            ->icon($icon)
            ->exports(ExportService::getExportAllActionConfig($tableName));
    }

    /**
     * Create an ExportBulkAction for bulk actions (exports selected records)
     */
    public static function createExportBulkAction(string $tableName, ?string $label = null): ExportBulkAction
    {
        return ExportBulkAction::make('export_selected')
            ->exports(ExportService::getExportBulkActionConfig($tableName));
    }
}

