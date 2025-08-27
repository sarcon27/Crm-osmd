<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreSectionDto extends DataTransferObject
{
    public string $name;

    public int $floors_count;

    public ?int $id = null;
}
