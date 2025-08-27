<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\InvoiceResources;

use App\Http\Resources\Dashboard\MeasureResources\MeasureResource;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResource;
use App\Http\Resources\Dashboard\TariffResources\TariffResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
            'service' => $this->whenLoaded('service', fn () => new ServiceResource($this->service)),
            'tariff' => $this->whenLoaded('tariff', fn () => new TariffResource($this->service)),
            'measure' => $this->whenLoaded('measure', fn () => new MeasureResource($this->measure)),
            'created_at' => $this->created_at?->format('Y-m-d'),

        ];
    }
}
