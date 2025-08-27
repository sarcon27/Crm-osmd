<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class StoreInvoiceApartmentDto extends DataTransferObject
{
    public int $apartment_id;
}
