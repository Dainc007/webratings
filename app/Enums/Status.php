<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Szkic',
            self::PUBLISHED => 'Opublikowany',
            self::ARCHIVED => 'Zarchiwizowany',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'info',      // Blue
            self::PUBLISHED => 'success', // Green
            self::ARCHIVED => 'danger',   // Red
        };
    }

    /**
     * Get all status options for forms
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->getLabel();
        }
        return $options;
    }
} 