<?php

declare(strict_types=1);

namespace App\Enums;

enum IonizerType: string
{
    case PLASMA = 'plasma';
    case BLADE = 'blade';

    /**
     * Get all ionizer type options for forms
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $type) {
            $options[$type->value] = $type->getLabel();
        }

        return $options;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PLASMA => 'Jonizator Plazmowy',
            self::BLADE => 'Jonizator ostrzowy',
        };
    }
}
