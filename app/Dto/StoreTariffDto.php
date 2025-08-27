<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\TariffStatusEnum;
use App\Enums\TariffTypeEnum;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreTariffDto extends DataTransferObject
{
    public string $start_date;

    public ?string $end_date;

    public float $price;

    public int $measure_id;

    public int $service_id;

    public string|TariffStatusEnum $status;

    public string|TariffTypeEnum $type;

    public ?string $notes;
}
