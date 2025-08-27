<?php

declare(strict_types=1);

namespace App\Pipelines\CreateUserProcess;

use App\Dto\StoreThirdPartyDto;
use App\Models\ThirdParty;

class CreateThirdParty
{
    public function handle(array $payload, \Closure $next)
    {
        $data = [
            'tenant_id' => $payload['tenant']->id,
            'name' => 'Банк',
        ];

        $dtoBank = new StoreThirdPartyDto(...$data);

        $dataProvider = [
            'tenant_id' => $payload['tenant']->id,
            'name' => 'Поставщик',
        ];

        $dtoProvider = new StoreThirdPartyDto(...$dataProvider);

        $payload['bank'] = ThirdParty::query()->create($dtoBank->toArray());
        $payload['provider'] = ThirdParty::query()->create($dtoProvider->toArray());

        return $next($payload);
    }
}
