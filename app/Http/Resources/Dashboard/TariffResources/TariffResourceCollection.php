<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\TariffResources;

use App\Enums\TariffStatusEnum;
use App\Enums\TariffTypeEnum;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TariffResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(fn ($apartment) => new TariffResource($apartment)),
            'meta' => [
                'statusOptions' => collect(TariffStatusEnum::cases())
                    ->map(fn ($status) => [
                        'value' => $status->value,
                        'label' => $status->label(),
                    ]),
                'typeOptions' => collect(TariffTypeEnum::cases())
                    ->map(fn ($status) => [
                        'value' => $status->value,
                        'label' => $status->label(),
                    ]),
            ],
        ];
    }
}
