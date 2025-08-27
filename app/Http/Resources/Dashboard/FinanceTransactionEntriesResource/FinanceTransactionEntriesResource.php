<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\FinanceTransactionEntriesResource;

use App\Http\Resources\Dashboard\InvoiceResources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTransactionEntriesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'notes' => $this->notes,
            'invoice' => $this->whenLoaded('invoice', fn () => new InvoiceResource($this->invoice)),
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
