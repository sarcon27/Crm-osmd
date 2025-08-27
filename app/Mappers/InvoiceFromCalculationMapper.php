<?php

namespace App\Mappers;

use App\Dto\InvoiceDto;
use App\Dto\InvoiceItemDto;
use InvalidArgumentException;
use Spatie\DataTransferObject\DataTransferObject;

class InvoiceFromCalculationMapper implements MapperInterface
{
    public function map(DataTransferObject $dto): array
    {
        if (! $dto instanceof InvoiceDto) {
            throw new InvalidArgumentException('Expected InvoiceDto');
        }

        $invoiceData = [
            'apartment_id' => $dto->apartment_id,
            'apartment_owner_id' => $dto->apartment_owner_id,
            'period' => $dto->period,
            'total' => $dto->total,
            'debt' => $dto->debt ?? 0,
            'initial_finance_transaction_entry_id' => $dto->initial_finance_transaction_entry_id,
        ];

        $itemsData = array_map(function (InvoiceItemDto $item) {
            return [
                'service_id' => $item->service_id,
                'tariff_id' => $item->tariff_id,
                'measure_id' => $item->measure_id,
                'amount' => $item->amount,
                'quantity' => $item->quantity,
            ];
        }, $dto->items);

        return [
            'invoice' => $invoiceData,
            'items' => $itemsData,
        ];
    }
}
