<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

interface SettingRepositoryService
{
    public function get(): Collection;

    public static function getSetting(string $value): string;

    public function save(DataTransferObject $dto): void;
}
