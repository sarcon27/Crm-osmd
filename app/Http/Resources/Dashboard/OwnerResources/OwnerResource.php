<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\OwnerResources;

use App\Http\Resources\Dashboard\ApartmentResources\ApartmentResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_payer' => (bool) $this->pivot?->is_payer ?? false,
            'payment_percent' => $this->pivot?->payment_percent ?? 0,
            'apartments_count' => $this->apartments_count,
            'apartments' => $this->whenLoaded('apartments', fn () => new ApartmentResourceCollection($this->apartments)),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),

        ];
    }
}
