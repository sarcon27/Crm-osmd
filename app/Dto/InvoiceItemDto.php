<?php

namespace App\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class InvoiceItemDto extends DataTransferObject
{
    public int $service_id;

    public int $tariff_id;

    public int $measure_id;

    public float $quantity;

    public float $amount;
}
