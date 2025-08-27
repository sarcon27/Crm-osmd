<?php

declare(strict_types=1);

namespace App\Enums;

enum TariffTypeEnum: string
{
    case Meter = 'meter';
    case TotalArea = 'total_area';
    case LivingArea = 'living_area';
    case Rooms = 'rooms';
    case Floor = 'floor';
    case Fixed = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::Meter => 'По счетчику',
            self::TotalArea => 'По общей площади',
            self::LivingArea => 'По жилой площади',
            self::Rooms => 'По кол-ву комнат',
            self::Floor => 'По кол-ву этажей',
            self::Fixed => 'Фиксированная',

        };
    }
}
