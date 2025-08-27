<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\FinanceTransactionStatusEnum;
use Spatie\DataTransferObject\DataTransferObject;

class StoreFinanceTransactionDto extends DataTransferObject
{
    public int $debit_account_id;

    public int $credit_account_id;

    public float $total;

    public ?string $status = FinanceTransactionStatusEnum::Opened->value;

    public string $name;
}
