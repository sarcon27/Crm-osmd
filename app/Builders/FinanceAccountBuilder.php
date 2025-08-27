<?php

declare(strict_types=1);

namespace App\Builders;

use App\Dto\StoreFinanceAccountDto;
use App\Enums\FinanceOwnerTypesEnum;
use App\Enums\FinanceTypesEnum;
use App\Models\Apartment;
use App\Models\Company;
use App\Models\FinanceAccount;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;

final class FinanceAccountBuilder
{
    protected ?string $number = null;

    protected string $financeOwnerType;

    protected int $financeOwnerId;

    protected string $financeType = FinanceTypesEnum::Active->value;

    protected float $debit = 0;

    protected float $credit = 0;

    protected ?int $tenant_id = null;

    protected ?string $name = null;

    public static function builder(): self
    {
        return new self;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function apartmentOwner(Apartment $owner): self
    {
        $this->financeOwnerId = $owner->id;
        $this->financeOwnerType = FinanceOwnerTypesEnum::Apartment->value;
        $this->number = $this->generateNumberApartment($owner);
        $this->name = 'Особовий рахунок';

        return $this;
    }

    public function companyOwner(Company $company): self
    {
        $this->financeOwnerType = FinanceOwnerTypesEnum::Company->value;
        $this->financeOwnerId = $company->id;

        return $this;
    }

    public function thirdPartyOwner(Model $owner): self
    {
        $this->financeOwnerType = FinanceOwnerTypesEnum::ThirdParty->value;
        $this->financeOwnerId = $owner->id;

        return $this;
    }

    public function financeType(string $financeType): self
    {
        $this->financeType = $financeType;

        return $this;
    }

    public function setDebit(float $value): self
    {
        $this->debit = $value;

        return $this;
    }

    public function setCredit(float $value): self
    {
        $this->credit = $value;

        return $this;
    }

    public function setTenant(Tenant $tenant): self
    {
        $this->tenant_id = $tenant->id;

        return $this;
    }

    private function generateNumberApartment(Apartment $apartment): string
    {
        return $apartment->tenant_id
            .$apartment->building_id
            .$apartment->section_id
            .$apartment->id
            .$apartment->number;
    }

    public function create(): FinanceAccount
    {
        $data = [
            'number' => $this->number ?? uniqid(),
            'name' => $this->name,
            'finance_owner_type' => $this->financeOwnerType,
            'finance_owner_id' => $this->financeOwnerId,
            'finance_type' => $this->financeType,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'balance' => $this->debit - $this->credit,
        ];

        $dto = new StoreFinanceAccountDto(...$data);

        $dto = $dto->toArray();

        // TODO Подумать, почему проблема No current tenant identified, это костыль
        if ($this->tenant_id) {
            $dto['tenant_id'] = $this->tenant_id;
        }

        return FinanceAccount::query()->create($dto);
    }
}
