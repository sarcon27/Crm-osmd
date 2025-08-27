<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\ThirdPartyResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ThirdPartyResourceCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
