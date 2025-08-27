<?php

declare(strict_types=1);

namespace App\Services\Factory\CalculateServices;

use App\Dto\TariffCalculationResultDto;
use App\Models\Apartment;
use App\Models\Tariff;

readonly class FixedTariffCalculator implements TariffCalculatorInterface
{
    public function calculate(Apartment $apartment, Tariff $tariff): TariffCalculationResultDto
    {
        $quantity = 1;
        $amount = (float) $tariff->price;

        return new TariffCalculationResultDto(
            quantity: $quantity,
            amount: $amount);
    }
}
