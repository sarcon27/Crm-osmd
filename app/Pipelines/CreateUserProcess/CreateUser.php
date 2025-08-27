<?php

namespace App\Pipelines\CreateUserProcess;

use App\Dto\StoreUserDto;
use App\Services\UserService;

class CreateUser
{
    public function __construct(private readonly UserService $service) {}

    public function handle(array $payload, \Closure $next)
    {
        $payload['user']['tenant'] = $payload['tenant'];
        $dto = new StoreUserDto(...$payload['user']);

        $this->service->create($dto);

        return $next($payload);
    }
}
