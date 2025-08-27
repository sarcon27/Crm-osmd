<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TariffTypeEnum;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

final class ServiceService extends BaseService
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->query()->with('tariff')->orderByDesc('id')->get();
    }

    public function getBasicServices(): Collection
    {
        return $this->model->query()->basic()->orderByDesc('id')->get();
    }

    public function getExtraServices(): Collection
    {
        return $this->model->query()->extra()->orderByDesc('id')->get();
    }

    public function getByTariffType(TariffTypeEnum $type): Collection
    {
        return $this->model->query()->byTariffType($type)->get();
    }
}
