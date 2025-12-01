<?php

declare(strict_types=1);

namespace App\Enums;

enum DehumidifierFunction: string
{
    case PARENTAL_LOCK = 'blokada_rodzicielska';
    case FILTER_CLEANING_REMINDER = 'przypomnienie_o_czyszczeniu_flitra';
    case WHEELS = 'kolka_jezdne';
    case PERCENTAGE_DISPLAY = 'wyswietlacz_procent';
    case BACKLIGHT_DIMMING = 'wygaszanie_podswietlenia';
    case TIMER = 'timer';
    case SLEEP_MODE = 'tryb_sleep';

    /**
     * Get all dehumidifier function options for forms
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $function) {
            $options[$function->value] = $function->getLabel();
        }

        return $options;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PARENTAL_LOCK => 'Blokada rodzicielska',
            self::FILTER_CLEANING_REMINDER => 'Przypomnienie o czyszczeniu flitra',
            self::WHEELS => 'Kółka jezdne',
            self::PERCENTAGE_DISPLAY => 'Wyświetlacz %',
            self::BACKLIGHT_DIMMING => 'Wygaszanie podświetlenia',
            self::TIMER => 'Timer (programator czasowy)',
            self::SLEEP_MODE => 'Tryb sleep (tryb nocny)',
        };
    }
}
