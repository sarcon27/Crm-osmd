<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tariff;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

final class TariffService extends BaseService
{
    public function __construct(Tariff $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->with(['measure', 'service'])
            ->allowedFilters([
                'name',
                'status',
                'type',
                'is_base',
                'start_date',
                'end_date',
            ])
            ->allowedSorts([
                'name',
                'status',
                'type',
                'is_base',
                'start_date',
                'end_date',
                'created_at',
            ])
            ->defaultSort('-id')
            ->paginate();
    }
}
