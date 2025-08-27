<?php

declare(strict_types=1);

namespace App\Pipelines\CreateUserProcess;

use App\Dto\StoreCompanyDto;
use App\Models\Company;

class CreateCompany
{
    public function handle(array $payload, \Closure $next)
    {
        $data = [
            'tenant_id' => $payload['tenant']->id,
            'name' => $payload['tenant']->name,
        ];

        $dto = new StoreCompanyDto(...$data);

        $payload['company'] = Company::query()->create($dto->toArray());

        return $next($payload);
    }
}
