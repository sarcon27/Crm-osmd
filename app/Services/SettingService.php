<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SettingKeys;
use App\Services\Interfaces\SettingRepositoryService;
use Illuminate\Support\Collection;
use Rawilk\Settings\Facades\Settings;
use Spatie\DataTransferObject\DataTransferObject;

final class SettingService implements SettingRepositoryService
{
    public function get(): Collection
    {
        return collect(SettingKeys::cases())->mapWithKeys(fn ($key) => [
            $key->value => Settings::get($key->value, ''),
        ]);

    }

    public static function getSetting(string $value): string
    {
        return Settings::get($value, '');
    }

    public function save(DataTransferObject $dto): void
    {
        Settings::set($dto->key, $dto->value);
    }
}
