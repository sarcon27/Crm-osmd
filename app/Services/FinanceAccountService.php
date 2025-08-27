<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\FinanceAccountBuilder;
use App\Enums\FinanceOwnerTypesEnum;
use App\Models\Apartment;
use App\Models\FinanceAccount;
use App\Services\Interfaces\AccountRepositoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\QueryBuilder;

class FinanceAccountService extends BaseService implements AccountRepositoryService
{
    public function __construct(FinanceAccount $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->with(['financeOwner.building'])
            ->where('finance_owner_type', FinanceOwnerTypesEnum::Apartment)
            ->allowedFilters([
            ])
            ->allowedSorts([
            ])
            ->defaultSort('-id')
            ->paginate(25);
    }

    public function searchForAutocomplite(DataTransferObject $qto): Collection
    {
        $query = $qto->q;

        return $this->model::query()
            ->with(['financeOwner'])
            ->where('number', 'like', "%{$query}%")
            ->get();
    }

    public function getMainAccounts(): Collection
    {
        return FinanceAccount::query()->with('financeOwner')->main()->get();
    }

    public function createFromApartment(Apartment $apartment): void
    {
        FinanceAccountBuilder::builder()
            ->apartmentOwner($apartment)
            ->create();
    }
}
