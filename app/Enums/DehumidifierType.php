<?php

declare(strict_types=1);

namespace App\Enums;

enum DehumidifierType: string
{
    case CONDENSATION = 'kondensacyjny';
    case ADSORPTION = 'adsorpcyjny';
    case PELTIER = 'z ogniwem Peltiera';
    case AIR = 'powietrza';
    case WITH_PURIFIER = 'z oczyszczaczem';
    case WITH_IONIZER = 'z jonizatorem';

    /**
     * Get all dehumidifier type options for forms
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
            self::CONDENSATION => 'Osuszacz kondensacyjny',
            self::ADSORPTION => 'Osuszacz adsorpcyjny',
            self::PELTIER => 'Osuszacz z ogniwem Peltiera',
            self::AIR => 'Osuszacz powietrza',
            self::WITH_PURIFIER => 'Osuszacz z oczyszczaczem',
            self::WITH_IONIZER => 'Osuszacz z jonizatorem',
        };
    }
}
