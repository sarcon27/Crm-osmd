<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingKeys: string
{
    case SiteName = 'site_name';
    // case Logo = 'logo';

    public static function values(): array
    {
        return array_map(fn (self $key) => $key->value, self::cases());
    }
}
