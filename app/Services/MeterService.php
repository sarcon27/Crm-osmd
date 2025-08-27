<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Apartment;
use App\Models\Meter;
use App\Models\Service;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class MeterService extends BaseService
{
    public function __construct(Meter $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->with(['service', 'apartment.building'])
            ->allowedFilters([
                'period',
                'apartment_id',
                'service_id',
                AllowedFilter::callback('address', function ($query, $value) {
                    $query->whereHas('apartment.building', function ($q) use ($value) {
                        $q->where('address', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('number', function ($query, $value) {
                    $query->whereHas('apartment', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    });
                }),
            ])
            ->allowedSorts([
            ])
            ->defaultSort('-id')
            ->paginate(15);
    }

    public function create(DataTransferObject $dto): Model
    {
        $model = $this->model;

        DB::beginTransaction();
        try {
            $lastMeter = Meter::query()->where('apartment_id', $dto->apartment_id)
                ->where('service_id', $dto->service_id)
                ->orderByDesc('period')
                ->first();

            $data = $dto->toArray();

            $period = $data['is_next_period']
                ? Carbon::now()->addMonth()->format('Y-m')
                : Carbon::now()->format('Y-m');

            $data['period'] = $period;
            $data['old_value'] = $lastMeter?->new_value ?? 0;

            $model->fill($data);
            if (! $model->save()) {
                throw new Exception('Can`t save model');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $model;
    }

    // Показание
    public function getConsumption(Apartment $apartment, Service $service): float
    {
        $lastMeter = $this->model::query()
            ->where('apartment_id', $apartment->id)
            ->where('service_id', $service->id)
            ->where('period', now()->format('Y-m'))
            ->first();

        if (! $lastMeter) {
            return 0;
        }

        return max(0, $lastMeter->new_value - $lastMeter->old_value);
    }
}
