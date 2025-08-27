<?php

namespace App\Jobs;

use App\Models\FinanceTransaction;
use App\Services\FinanceTransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PostFinanceTransactionJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(private readonly FinanceTransaction $transaction) {}

    public function handle(FinanceTransactionService $service)
    {
        $service->post($this->transaction);
    }
}
