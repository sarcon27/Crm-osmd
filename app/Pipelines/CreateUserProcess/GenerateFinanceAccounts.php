<?php

namespace App\Pipelines\CreateUserProcess;

use App\Builders\FinanceAccountBuilder;

class GenerateFinanceAccounts
{
    public function handle(array $payload, \Closure $next)
    {
        // dd($payload['bank']);
        FinanceAccountBuilder::builder()
            ->companyOwner($payload['company'])
            ->setTenant($payload['tenant'])

            ->setNumber('3771')
            ->setName('Розрахунки з іншими дебіторами')
            ->create();

        FinanceAccountBuilder::builder()
            ->thirdPartyOwner($payload['bank'])
            ->setTenant($payload['tenant'])

            ->setNumber('311')
            ->setName('Поточні рахунки в національній валюті')
            ->create();

        FinanceAccountBuilder::builder()
            ->thirdPartyOwner($payload['provider'])
            ->setTenant($payload['tenant'])

            ->setNumber('631')
            ->setName('Розрахунки з постачальниками')
            ->create();

        return $next($payload);
    }
}
