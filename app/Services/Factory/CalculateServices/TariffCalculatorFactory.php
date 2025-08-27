<?php

declare(strict_types=1);

namespace App\Services\Factory\CalculateServices;

use App\Dto\TariffCalculationResultDto;
use App\Enums\TariffTypeEnum;
use App\Models\Apartment;
use App\Models\Tariff;

class TariffCalculatorFactory
{
    public static function getCalculator(Tariff $tariff): TariffCalculatorInterface
    {
        return match ($tariff->type) {
            TariffTypeEnum::Meter => new MeterTariffCalculator,
            TariffTypeEnum::TotalArea => new TotalAreaTariffCalculator,
            TariffTypeEnum::LivingArea => new LivingAreaTariffCalculator,
            TariffTypeEnum::Rooms => new RoomsTariffCalculator,
            TariffTypeEnum::Floor => new FloorTariffCalculator,
            TariffTypeEnum::Fixed => new FixedTariffCalculator,
        };
    }

    public static function calculate(Tariff $tariff, Apartment $apartment): TariffCalculationResultDto
    {
        $calculator = self::getCalculator($tariff);

        return $calculator->calculate($apartment, $tariff);
    }
}
