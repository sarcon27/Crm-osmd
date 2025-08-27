<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\ApartmentResources;

use App\Enums\AppartamentStatusEnum;
use App\Enums\AppartamentTypeEnum;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApartmentResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(fn ($apartment) => new ApartmentResource($apartment)),
            'meta' => [
                'statusOptions' => collect(AppartamentStatusEnum::cases())
                    ->map(fn ($status) => [
                        'value' => $status->value,
                        'label' => $status->label(),
                    ]),
                'typeOptions' => collect(AppartamentTypeEnum::cases())
                    ->map(fn ($status) => [
                        'value' => $status->value,
                        'label' => $status->label(),
                    ]),
            ],
        ];
    }
}
