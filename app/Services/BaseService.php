<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\EloquentRepositoryService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseService implements EloquentRepositoryService
{
    public function __construct(protected Model $model) {}

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->allowedFilters([
            ])
            ->allowedSorts([
            ])
            ->defaultSort('-id')
            ->paginate(15);
    }

    public function all(): Collection
    {
        return $this->model->query()->orderByDesc('id')->get();
    }

    public function create(DataTransferObject $dto): Model
    {
        $model = $this->model;

        DB::beginTransaction();
        try {
            $model->fill($dto->toArray());
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

    public function update(int $id, DataTransferObject $dto): Model
    {
        $model = $this->find($id);

        DB::beginTransaction();
        try {
            $model->fill($dto->toArray());
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

    /**
     * @throws Exception|\Throwable
     */
    public function destroy(int $id): bool
    {
        $model = $this->find($id);
        DB::beginTransaction();
        try {
            if (! $model->delete()) {
                throw new Exception('Can`t delete model');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return true;
    }

    public function find($id): ?Model
    {
        return $this->model->query()->find($id);
    }
}
