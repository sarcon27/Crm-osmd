<?php

declare(strict_types=1);

namespace App\Dto;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreOwnerDto extends DataTransferObject
{
    public string $name;

    public ?string $phone;

    public ?string $email;

    public ?string $notes;

    public ?int $id = null;

    public bool $is_payer = false;

    public ?float $payment_percent = 0.00;
}
