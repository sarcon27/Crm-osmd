<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\FinanceTypesEnum;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreFinanceAccountDto extends DataTransferObject
{
    public int $finance_owner_id;

    public string $finance_owner_type;

    public ?string $finance_type = FinanceTypesEnum::Active->value;

    public ?string $name;

    public string $number;

    public ?float $debit = 0;

    public ?float $credit = 0;

    public ?float $balance = 0;
}
