<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\MeterResources;

use App\Http\Resources\Dashboard\ApartmentResources\ApartmentResource;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'new_value' => $this->new_value,
            'old_value' => $this->old_value,
            'period' => $this->period,
            'service' => $this->whenLoaded('service', fn () => new ServiceResource($this->service)),
            'apartment' => $this->whenLoaded('apartment', fn () => new ApartmentResource($this->apartment)),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),

        ];
    }
}
