<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\FinanceTransactionResources;

use App\Enums\FinanceTransactionStatusEnum;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceTransactionResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(fn ($apartment) => new FinanceTransactionResource($apartment)),
            'meta' => [
                'statusOptions' => collect(FinanceTransactionStatusEnum::cases())
                    ->values()
                    ->map(fn ($option) => [
                        'value' => $option->value,
                        'label' => $option->label(),
                    ]),
            ],
        ];
    }
}
