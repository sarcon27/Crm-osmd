<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreMeterDto extends DataTransferObject
{
    public int $apartment_id;

    public int $service_id;

    public float $new_value;

    public ?float $old_value = 0;

    public ?string $period;

    public ?bool $is_next_period = false;
}
