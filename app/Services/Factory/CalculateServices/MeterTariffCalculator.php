<?php

declare(strict_types=1);

namespace App\Services\Factory\CalculateServices;

use App\Dto\TariffCalculationResultDto;
use App\Models\Apartment;
use App\Models\Tariff;
use App\Services\MeterService;

readonly class MeterTariffCalculator implements TariffCalculatorInterface
{
    public function calculate(Apartment $apartment, Tariff $tariff): TariffCalculationResultDto
    {
        $consumption = (float) app(MeterService::class)->getConsumption($apartment, $tariff->service);
        $amount = round($consumption * (float) $tariff->price, 2);

        return new TariffCalculationResultDto(
            quantity: $consumption,
            amount: $amount);
    }
}
