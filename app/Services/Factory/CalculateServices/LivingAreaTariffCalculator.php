<?php

declare(strict_types=1);

namespace App\Services\Factory\CalculateServices;

use App\Dto\TariffCalculationResultDto;
use App\Models\Apartment;
use App\Models\Tariff;

readonly class LivingAreaTariffCalculator implements TariffCalculatorInterface
{
    public function calculate(Apartment $apartment, Tariff $tariff): TariffCalculationResultDto
    {
        $quantity = (float) ($apartment->living_area ?? 0);
        $amount = $quantity * (float) ($tariff->price ?? 0);

        return new TariffCalculationResultDto(...$quantity, $amount);
    }
}
