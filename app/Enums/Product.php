<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\AirConditioner;
use App\Models\AirHumidifier;
use App\Models\AirPurifier;
use App\Models\Dehumidifier;
use App\Models\Sensor;
use App\Models\UprightVacuum;
use Illuminate\Database\Eloquent\Builder;

enum Product: string
{
    case AIR_PURIFIERS = 'air_purifiers';
    case AIR_HUMIDIFIERS = 'air_humidifiers';
    case AIR_CONDITIONERS = 'air_conditioners';
    case DEHUMIDIFIERS = 'dehumidifiers';
    case SENSORS = 'sensors';
    case UPRIGHT_VACUUMS = 'upright_vacuums';

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getQueryForType(string $type): ?Builder
    {
        $product = self::tryFrom($type);

        return $product?->getQuery();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::AIR_PURIFIERS => 'Oczyszczacz Powietrza',
            self::AIR_HUMIDIFIERS => 'Nawilżacz Powietrza',
            self::AIR_CONDITIONERS => 'Klimatyzator',
            self::DEHUMIDIFIERS => 'Osuszacz Powietrza',
            self::SENSORS => 'Czujnik',
            self::UPRIGHT_VACUUMS => 'Odkurzacz Pionowy',
        };
    }

    public function getModelClass(): string
    {
        return match ($this) {
            self::AIR_PURIFIERS => AirPurifier::class,
            self::AIR_HUMIDIFIERS => AirHumidifier::class,
            self::AIR_CONDITIONERS => AirConditioner::class,
            self::DEHUMIDIFIERS => Dehumidifier::class,
            self::SENSORS => Sensor::class,
            self::UPRIGHT_VACUUMS => UprightVacuum::class,
        };
    }

    public function getQuery(): Builder
    {
        $modelClass = $this->getModelClass();

        return $modelClass::query();
    }
}
