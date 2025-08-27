<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\ApartmentResources;

use App\Http\Resources\Dashboard\BuildingResources\BuildingResource;
use App\Http\Resources\Dashboard\FinanceAccountResources\FinanceAccountResource;
use App\Http\Resources\Dashboard\OwnerResources\OwnerResourceCollection;
use App\Http\Resources\Dashboard\SectionResources\SectionResource;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'building_id' => $this->building_id,
            'section_id' => $this->section_id,
            'number' => $this->number,
            'floor' => $this->floor,
            'total_area' => $this->total_area,
            'living_area' => $this->living_area,
            'registered_residents' => $this->registered_residents,
            'rooms_count' => $this->rooms_count,
            'status' => $this->status ? [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ] : null,
            'type' => $this->type ? [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ] : null,
            'ownership_type' => $this->ownership_type,
            'is_payer' => (bool) $this->pivot?->is_payer ?? false,
            'payment_percent' => $this->pivot?->payment_percent ?? 0,
            'notes' => $this->description,
            'section' => $this->whenLoaded('section', fn () => new SectionResource($this->section)),
            'building' => $this->whenLoaded('building', fn () => new BuildingResource($this->building)),
            'owners' => $this->whenLoaded('owners', fn () => new OwnerResourceCollection($this->owners)),
            'extraServices' => $this->whenLoaded('extraServices', fn () => new ServiceResourceCollection($this->extraServices)),
            'financeAccount' => $this->whenLoaded('financeAccount', fn () => new FinanceAccountResource($this->financeAccount)),
            'created_at' => $this->created_at?->format('Y-m-d'),

        ];
    }
}
