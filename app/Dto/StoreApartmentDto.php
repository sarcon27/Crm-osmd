<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\AppartamentStatusEnum;
use App\Enums\AppartamentTypeEnum;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreApartmentDto extends DataTransferObject
{
    public string $number;

    public int $floor;

    public float $total_area;

    public float $living_area;

    public int $rooms_count;

    public int $registered_residents;

    public string|AppartamentStatusEnum $status;

    public string|AppartamentTypeEnum $type;

    public int $section_id;

    public int $building_id;

    public ?string $notes = null;

    /** @var \App\Dto\StoreOwnerDto[] */
    #[CastWith(ArrayCaster::class, itemType: StoreOwnerDto::class)]
    public array $owners = [];

    public ?array $extraServices = [];
}
