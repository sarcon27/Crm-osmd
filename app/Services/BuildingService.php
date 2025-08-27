<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Building;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\QueryBuilder;

final class BuildingService extends BaseService
{
    public function __construct(Building $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->with(['sections', 'services'])
            ->withCount('sections')
            ->allowedFilters([
                'name',
                'address',
            ])
            ->allowedSorts([
                'name',
                'address',
                'id',
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

            $model->sections()->createMany($data['sections']);

            $model->services()->sync($data['services']);

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

            // ID существующих секций в БД
            $existingIds = $model->sections()->pluck('id')->toArray();

            // ID, которые пришли в запросе (существующие)
            $submittedIds = collect($data['sections'])
                ->filter(fn ($s) => ! empty($s['id']))
                ->pluck('id')
                ->toArray();

            // Удаляем отсутствующие в запросе
            $toDelete = array_diff($existingIds, $submittedIds);
            if ($toDelete) {
                $model->sections()->whereIn('id', $toDelete)->delete();
            }

            // Обрабатываем каждую секцию
            foreach ($data['sections'] as $section) {
                if (! empty($section['id'])) {
                    // Обновляем только изменённые поля
                    $updateData = collect($section)
                        ->only(['name', 'floors_count']) // разрешённые поля
                        ->filter(fn ($v) => ! is_null($v)) // исключаем null, если не хотим перезаписывать
                        ->toArray();

                    if (! empty($updateData)) {
                        $model->sections()->where('id', $section['id'])->update($updateData);
                    }
                } else {
                    // Создаём новую секцию
                    $model->sections()->create([
                        'name' => $section['name'] ?? '',
                        'floors_count' => $section['floors_count'] ?? 0,
                    ]);
                }
            }

            $model->services()->sync($data['services']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $model;
    }

    public function allWithSections(): Collection
    {
        return $this->model->query()->with('sections')->orderByDesc('id')->get();
    }
}
