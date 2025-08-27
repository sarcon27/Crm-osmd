<?php

declare(strict_types=1);

namespace App\Enums;

enum AppartamentStatusEnum: string
{
    case Occupied = 'occupied';
    case Free = 'free';
    case Renovation = 'renovation';
    case Rent = 'rent';

    public function label(): string
    {
        return match ($this) {
            self::Occupied => 'Заселена',
            self::Free => 'Свободна',
            self::Renovation => 'Ремонт',
            self::Rent => 'В аренде',
        };
    }
}
