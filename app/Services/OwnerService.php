<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\QueryBuilder;

final class OwnerService extends BaseService
{
    public function __construct(Owner $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->withCount('apartments')
            ->with(['apartments.section', 'apartments.building'])
            ->allowedFilters([
                'name',
                'phone',
                'email',
            ])
            ->allowedSorts([
                'name',
                'id',
                'phone',
                'email',
            ])
            ->defaultSort('-id')
            ->paginate(15);
    }

    public function searchForAutocomplite(DataTransferObject $qto): Collection
    {
        $query = $qto->q;

        return $this->model::query()
            ->select('id', 'name', 'email', 'phone')
            ->when($query, fn ($q) => $q->where(function ($q2) use ($query) {
                $q2->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            }))
            ->get();
    }
}
