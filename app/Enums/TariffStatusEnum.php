<?php

declare(strict_types=1);

namespace App\Enums;

enum TariffStatusEnum: string
{
    case Active = 'active';
    case Archive = 'archive';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Действующий',
            self::Archive => 'Архивный',
            self::Inactive => 'Выключен',
        };
    }
}
