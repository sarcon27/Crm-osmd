<?php

namespace App\Pipelines\CreateUserProcess;

use App\Dto\StoreTenantDto;
use App\Models\Tenant;

class CreateTenant
{
    public function handle(array $payload, \Closure $next)
    {
        $dto = new StoreTenantDto(...$payload['tenant']);

        $payload['tenant'] = Tenant::query()->create($dto->toArray());

        return $next($payload);
    }
}
