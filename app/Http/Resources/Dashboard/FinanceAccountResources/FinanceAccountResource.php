<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\FinanceAccountResources;

use App\Enums\FinanceOwnerTypesEnum;
use App\Http\Resources\Dashboard\ApartmentResources\ApartmentResource;
use App\Http\Resources\Dashboard\CompanyResources\CompanyResource;
use App\Http\Resources\Dashboard\ThirdPartyResources\ThirdPartyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'finance_owner_type' => $this->finance_owner_type,
            'financeOwner' => $this->whenLoaded('financeOwner', function () {
                return match ($this->finance_owner_type) {
                    FinanceOwnerTypesEnum::Apartment => new ApartmentResource($this->financeOwner),
                    FinanceOwnerTypesEnum::Company => new CompanyResource($this->financeOwner),
                    FinanceOwnerTypesEnum::ThirdParty => new ThirdPartyResource($this->financeOwner),
                    default => null,
                };
            }),
            'name' => $this->name,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'balance' => $this->balance,
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),

        ];
    }
}
