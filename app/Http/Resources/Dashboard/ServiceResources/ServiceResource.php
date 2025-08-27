<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\ServiceResources;

use App\Http\Resources\Dashboard\TariffResources\TariffResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_auto' => $this->is_auto,
            'is_base' => $this->is_base,
            'name' => $this->name,
            'tariff' => $this->whenLoaded('tariff', fn () => new TariffResource($this->tariff)),
            'tariff_name' => $this->whenLoaded('tariff', function () {
                return $this->tariff
                    ? $this->tariff->price.' за '.($this->tariff->measure->name ?? '').' '.mb_strtolower($this->tariff->type?->label() ?? '')
                    : null;
            }),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),

        ];
    }
}
