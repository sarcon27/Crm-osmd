<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Apartment;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class ApartmentService extends BaseService
{
    public function __construct(Apartment $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->with(['section', 'building', 'owners', 'extraServices', 'financeAccount'])
            ->allowedFilters([
                AllowedFilter::exact('floor'),
                AllowedFilter::exact('number'),
                AllowedFilter::callback('finance_account_number', function ($query, $value) {
                    $query->whereHas('financeAccount', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('with_debt', function ($query, $value) {
                    if ($value) {
                        $query->whereHas('financeAccount', function ($q) {
                            $q->where('balance', '<', 0);
                        });
                    }
                }),
                'status',
                'type',
                'building_id',
                'section_id',
            ])
            ->allowedSorts([
                'status',
                'floor',
                'number',
                'section_id',
            ])
            ->defaultSort('-id')
            ->paginate(15);
    }

    public function create(DataTransferObject $dto): Model
    {
        $model = $this->model;
        $data = $dto->toArray();

        DB::beginTransaction();
        try {
            $model->fill($data);

            if (! $model->save()) {
                throw new Exception('Can`t save model');
            }

            $model->syncOwners($dto->owners);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $model;
    }

    public function update(int $id, DataTransferObject $dto): Model
    {
        $model = $this->find($id);
        $data = $dto->toArray();

        DB::beginTransaction();
        try {
            $model->fill($data);

            if (! $model->save()) {
                throw new Exception('Can`t save building');
            }

            $model->syncOwners($dto->owners);

            $model->extraServices()->sync($data['extraServices']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $model;
    }

    public function getServicesWithMeter(Apartment $apartment): Collection
    {
        return $apartment->servicesWithMeter();
    }
}
