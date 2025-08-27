<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\FinanceTransactionResources;

use App\Http\Resources\Dashboard\FinanceAccountResources\FinanceAccountResource;
use App\Http\Resources\Dashboard\FinanceTransactionEntriesResource\FinanceTransactionEntriesResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'status' => $this->status,
            'status' => $this->status ? [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ] : null,
            'name' => $this->name,
            'debit_account' => $this->whenLoaded('debit_account', fn () => new FinanceAccountResource($this->debit_account)),
            'credit_account' => $this->whenLoaded('credit_account', fn () => new FinanceAccountResource($this->credit_account)),
            'total' => $this->total,
            'entries' => $this->whenLoaded('entries', fn () => new FinanceTransactionEntriesResourceCollection($this->entries)),
            'posted_at' => $this->posted_at?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d'),

        ];
    }
}
