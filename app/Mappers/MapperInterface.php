<?php

declare(strict_types=1);

namespace App\Mappers;

use Spatie\DataTransferObject\DataTransferObject;

interface MapperInterface
{
    public function map(DataTransferObject $dto): array;
}
