<?php

namespace App\Services\Factory\CalculateServices;

use App\Dto\TariffCalculationResultDto;
use App\Models\Apartment;
use App\Models\Tariff;

readonly class FloorTariffCalculator implements TariffCalculatorInterface
{
    public function calculate(Apartment $apartment, Tariff $tariff): TariffCalculationResultDto
    {
        $quantity = (int) ($apartment->floor ?? 0);
        $amount = $quantity * (float) ($tariff->price ?? 0);

        return new TariffCalculationResultDto(...$quantity, $amount);
    }
}
