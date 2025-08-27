<?php

declare(strict_types=1);

namespace App\Enums;

enum FinanceTransactionStatusEnum: string
{
    case Opened = 'opened';

    case Posted = 'posted';

    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Opened => 'Открыта',
            self::Posted => 'Проведена',
            self::Rejected => 'Отклонена',
        };
    }
}
