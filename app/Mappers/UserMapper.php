<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Dto\StoreUserDto;
use Illuminate\Support\Facades\Hash;
use Spatie\DataTransferObject\DataTransferObject;

class UserMapper implements MapperInterface
{
    public function map(DataTransferObject $dto): array
    {
        if (! $dto instanceof StoreUserDto) {
            throw new \InvalidArgumentException('Expected StoreUserDto');
        }

        return [
            'tenant_id' => $dto->tenant->id,
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ];
    }
}
