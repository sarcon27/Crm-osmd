<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\FinanceAccountResources;

use App\Enums\FinanceOwnerTypesEnum;
use App\Http\Resources\Dashboard\CompanyResources\CompanyResourceCollection;
use App\Http\Resources\Dashboard\ThirdPartyResources\ThirdPartyResourceCollection;
use App\Models\Company;
use App\Models\ThirdParty;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceAccountResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(fn ($apartment) => new FinanceAccountResource($apartment)),
            'meta' => [
                'financeOwnerTypeOption' => collect(FinanceOwnerTypesEnum::cases())
                    ->filter(fn ($option) => $option !== FinanceOwnerTypesEnum::Apartment)
                    ->values()
                    ->map(fn ($option) => [
                        'value' => $option->value,
                        'label' => $option->label(),
                    ]),
                'companyListOptions' => new CompanyResourceCollection(Company::all()),
                'thirdPartyListOptions' => new ThirdPartyResourceCollection(ThirdParty::all()),
            ],
        ];
    }
}
