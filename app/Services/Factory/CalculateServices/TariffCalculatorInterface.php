<?php

declare(strict_types=1);

namespace App\Services\Factory\CalculateServices;

use App\Dto\TariffCalculationResultDto;
use App\Models\Apartment;
use App\Models\Tariff;

interface TariffCalculatorInterface
{
    public function calculate(Apartment $apartment, Tariff $tariff): TariffCalculationResultDto;
}
