<?php

namespace App\Enums;

enum ActivityLevelEnum: string
{
    case EASY = 'facil';
    case MEDIUM = 'media';
    case HARD = 'dificil';

    public function label(): string
    {
        return match ($this) {
            self::EASY => 'Facil',
            self::MEDIUM => 'Media',
            self::HARD => 'Dificil',
        };
    }

    public function pointsPerQuestion(): float
    {
        return match ($this) {
            self::EASY => 1.0,
            self::MEDIUM => 2.5,
            self::HARD => 4.0,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $level) => $level->value, self::cases());
    }
}
