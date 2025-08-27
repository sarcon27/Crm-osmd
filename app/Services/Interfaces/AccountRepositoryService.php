<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Apartment;

interface AccountRepositoryService
{
    public function createFromApartment(Apartment $apartment): void;
}
