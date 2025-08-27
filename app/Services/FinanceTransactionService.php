<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FinanceTransactionStatusEnum;
use App\Jobs\PostFinanceTransactionJob;
use App\Models\Apartment;
use App\Models\Company;
use App\Models\FinanceTransaction;
use App\Models\ThirdParty;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FinanceTransactionService extends BaseService
{
    public function __construct(FinanceTransaction $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
           // ->with(['debit_account.financeOwner', 'credit_account.financeOwner', 'entries'])
            ->with([
                'credit_account.financeOwner' => function ($morph) {
                    $morph->morphWith([
                        Apartment::class => ['building'],
                        Company::class => [],
                        ThirdParty::class => [],
                    ]);
                },
                'debit_account.financeOwner' => function ($morph) {
                    $morph->morphWith([
                        Apartment::class => ['building'],
                        Company::class => [],
                        ThirdParty::class => [],
                    ]);
                },
                'entries.invoice.items.service',
            ])
            ->allowedFilters([
                AllowedFilter::callback('debit_account_number', function ($query, $value) {
                    $query->whereHas('debit_account', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('credit_account_number', function ($query, $value) {
                    $query->whereHas('credit_account', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    });
                }),
                'status',
            ])
            ->allowedSorts([
            ])
            ->defaultSort('-id')
            ->paginate(25);
    }

    public function post(FinanceTransaction $transaction): void
    {
        if ($transaction->posted_at) {
            return;
        }

        DB::transaction(function () use ($transaction) {
            $this->applyDistributionByAccounts($transaction);
            $transaction->posted_at = now();
            $transaction->status = FinanceTransactionStatusEnum::Posted;
            $transaction->save();
        });
    }

    public function reject(FinanceTransaction $transaction): void
    {
        if ($transaction->posted_at) {
            return;
        }

        DB::transaction(function () use ($transaction) {
            $transaction->status = FinanceTransactionStatusEnum::Rejected;
            $transaction->save();
        });
    }

    public function postOnQueue(FinanceTransaction $transaction): void
    {
        PostFinanceTransactionJob::dispatch($transaction);
    }

    protected function applyDistributionByAccounts(FinanceTransaction $transaction): void
    {
        $transaction->load(['credit_account', 'debit_account']);
        $debitAccount = $transaction->debit_account;
        $creditAccount = $transaction->credit_account;

        $amount = $transaction->total;

        // Начисляем на дебет
        $debitAccount->debit += $amount;
        $debitAccount->balance += $amount;
        $debitAccount->save();

        // Списываем с кредита
        $creditAccount->credit += $amount;
        $creditAccount->balance -= $amount;
        $creditAccount->save();

    }
}
