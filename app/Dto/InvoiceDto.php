<?php

namespace App\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class InvoiceDto extends DataTransferObject
{
    public int $apartment_id;

    public int $apartment_owner_id;

    public string $period;

    public float $total;

    public ?float $debt = 0;

    public int $initial_finance_transaction_entry_id;

    /** @var InvoiceItemDto[] */
    public array $items;
}
