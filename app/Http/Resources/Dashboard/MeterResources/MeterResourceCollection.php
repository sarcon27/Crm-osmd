<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\MeterResources;

use App\Enums\TariffTypeEnum;
use App\Services\ServiceService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeterResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(fn ($apartment) => new MeterResource($apartment)),
            'meta' => [
                'serviceOptions' => app(ServiceService::class)->getByTariffType(TariffTypeEnum::Meter),
            ],
        ];
    }
}
