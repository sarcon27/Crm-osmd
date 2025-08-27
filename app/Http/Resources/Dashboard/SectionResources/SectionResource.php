<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\SectionResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'floors_count' => $this->floors_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
