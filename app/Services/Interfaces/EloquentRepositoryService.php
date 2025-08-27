<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\DataTransferObject;

interface EloquentRepositoryService
{
    public function search(): LengthAwarePaginator;

    public function all(): Collection;

    public function create(DataTransferObject $dto): Model;

    public function update(int $id, DataTransferObject $dto): Model;

    public function find($id): ?Model;

    public function destroy(int $id): bool;
}
