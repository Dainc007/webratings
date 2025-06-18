<?php
declare(strict_types=1);

namespace App\Enums;

enum ShortcodeOperator: string
{
    case EQUAL = '=';
    case GREATER = '>';
    case GREATER_EQUAL = '>=';
    case LESS = '<';
    case LESS_EQUAL = '<=';
    case NOT_EQUAL = '!=';
    case LIKE = 'like';

    public static function optionsForSelect(): array
    {
        return [
            self::EQUAL->value => 'Równe',
            self::GREATER->value => 'Większe',
            self::GREATER_EQUAL->value => 'Większe lub równe',
            self::LESS->value => 'Mniejsze',
            self::LESS_EQUAL->value => 'Mniejsze lub równe',
            self::NOT_EQUAL->value => 'Różne',
            self::LIKE->value => 'Zawiera',
        ];
    }

    public static function label(string $value): string
    {
        return match ($value) {
            self::EQUAL->value => 'Równe',
            self::GREATER->value => 'Większe',
            self::GREATER_EQUAL->value => 'Większe lub równe',
            self::LESS->value => 'Mniejsze',
            self::LESS_EQUAL->value => 'Mniejsze lub równe',
            self::NOT_EQUAL->value => 'Różne',
            self::LIKE->value => 'Zawiera',
            default => $value,
        };
    }
} 