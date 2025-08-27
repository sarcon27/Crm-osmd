<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreCompanyDto extends DataTransferObject
{
    public int $tenant_id;

    public string $name;
}
