<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\TariffResources;

use App\Http\Resources\Dashboard\MeasureResources\MeasureResource;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TariffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'measure_id' => $this->measure_id,
            'service_id' => $this->service_id,
            'measure' => $this->whenLoaded('measure', fn () => new MeasureResource($this->measure)),
            'service' => $this->whenLoaded('service', fn () => new ServiceResource($this->service)),
            'status' => $this->status ? [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ] : null,
            'type' => $this->type ? [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ] : null,
            'notes' => $this->notes,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),

        ];
    }
}
