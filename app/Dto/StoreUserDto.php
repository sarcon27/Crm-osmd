<?php

declare(strict_types=1);

namespace App\Dto;

use App\Models\Tenant;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StoreUserDto extends DataTransferObject
{
    public Tenant $tenant;

    public string $name;

    public string $email;

    public string $password;
}
