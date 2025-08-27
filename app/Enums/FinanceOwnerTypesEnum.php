<?php

declare(strict_types=1);

namespace App\Enums;

enum FinanceOwnerTypesEnum: string
{
    case Company = 'company';
    case Apartment = 'apartment';
    case ThirdParty = 'third_party';

    public function label(): string
    {
        return match ($this) {
            self::Company => __('finance.company'),
            self::ThirdParty => __('finance.third_party'),
            self::Apartment => __('finance.apartment'),
        };
    }
}
