<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\BuildingResources;

use App\Http\Resources\Dashboard\SectionResources\SectionResource;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'sections' => $this->whenLoaded('sections', fn () => SectionResource::collection($this->sections)),
            'services' => $this->whenLoaded('services', fn () => ServiceResource::collection($this->services)),
            'countSections' => $this->whenLoaded('sections', fn () => $this->sections_count),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at,
        ];
    }
}
