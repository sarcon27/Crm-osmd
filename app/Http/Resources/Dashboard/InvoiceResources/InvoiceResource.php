<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\InvoiceResources;

use App\Http\Resources\Dashboard\ApartmentResources\ApartmentResource;
use App\Http\Resources\Dashboard\FinanceTransactionEntriesResource\FinanceTransactionEntriesResource;
use App\Http\Resources\Dashboard\OwnerResources\OwnerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'debt' => $this->debt,
            'period' => $this->period,
            'items' => $this->whenLoaded('items', fn () => new InvoiceItemResourceCollection($this->items)),
            'apartment' => $this->whenLoaded('apartment', fn () => new ApartmentResource($this->apartment)),
            'owner' => $this->whenLoaded('owner', fn () => new OwnerResource($this->owner)),
            'transactionEntry' => $this->whenLoaded('transactionEntry', fn () => new FinanceTransactionEntriesResource($this->transactionEntry)),
            'created_at' => $this->created_at?->format('Y-m-d'),

        ];
    }
}
