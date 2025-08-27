<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\ThirdPartyResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThirdPartyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,

        ];
    }
}
