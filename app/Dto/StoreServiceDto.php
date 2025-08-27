<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreServiceDto extends DataTransferObject
{
    public string $name;

    public ?string $notes;

    public bool $is_auto;

    public bool $is_base;
}
