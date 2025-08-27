<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreBuildingDto extends DataTransferObject
{
    public string $name;

    public string $address;

    public ?string $description = null;

    /** @var \App\Dto\StoreSectionDto[] */
    #[CastWith(ArrayCaster::class, itemType: StoreSectionDto::class)]
    public array $sections = [];

    public array $services = [];
}
