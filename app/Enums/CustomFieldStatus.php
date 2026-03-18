<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CustomFieldStatus: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case FAILED = 'failed';
    case DELETING = 'deleting';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Oczekuje',
            self::ACTIVE => 'Aktywne',
            self::FAILED => 'Błąd',
            self::DELETING => 'Usuwanie',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACTIVE => 'success',
            self::FAILED => 'danger',
            self::DELETING => 'gray',
        };
    }
}
