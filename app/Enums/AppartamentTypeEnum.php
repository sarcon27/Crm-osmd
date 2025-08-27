<?php

declare(strict_types=1);

namespace App\Enums;

enum AppartamentTypeEnum: string
{
    case Commercial = 'commercial';
    case Residential = 'residential';

    public function label(): string
    {
        return match ($this) {
            self::Commercial => 'Коммерческая',
            self::Residential => 'Жилая',

        };
    }
}
