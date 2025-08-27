<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\FinanceAccountService;

final readonly class ProcessApartmentCreateAccount
{
    public function __construct(private FinanceAccountService $accountService) {}

    public function handle($event): void
    {
        $this->accountService->createFromApartment($event->apartment);
    }
}
