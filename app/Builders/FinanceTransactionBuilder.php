<?php

declare(strict_types=1);

namespace App\Builders;

use App\Enums\FinanceTransactionStatusEnum;
use App\Models\FinanceTransaction;
use App\Services\FinanceTransactionService;
use Illuminate\Support\Facades\DB;

class FinanceTransactionBuilder
{
    protected string $name;

    protected int $debitAccountId;

    protected int $creditAccountId;

    protected ?bool $posted = false;

    protected float $total = 0;

    protected array $entries = [];

    public static function builder(): self
    {
        return new self;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function debitAccount(int $accountId): self
    {
        $this->debitAccountId = $accountId;

        return $this;
    }

    public function creditAccount(int $accountId): self
    {
        $this->creditAccountId = $accountId;

        return $this;
    }

    public function total(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function posted($value = false): self
    {
        $this->posted = $value;

        return $this;
    }

    public function addEntry(float $amount, ?string $notes = null): self
    {
        $this->entries[] = [
            'amount' => $amount,
            'notes' => $notes,
        ];

        return $this;
    }

    public function save(): FinanceTransaction
    {
        return DB::transaction(function () {
            $transaction = FinanceTransaction::create([
                'status' => FinanceTransactionStatusEnum::Opened,
                'name' => $this->name,
                'debit_account_id' => $this->debitAccountId,
                'credit_account_id' => $this->creditAccountId,
                'posted_at' => null,
                'total' => $this->total,
            ]);

            foreach ($this->entries as $entryData) {
                $transaction->entries()->create([
                    'amount' => $entryData['amount'],
                    'notes' => $entryData['notes'] ?? null,
                ]);
            }

            if ($this->posted) {
                app(FinanceTransactionService::class)->post($transaction);
            }

            return $transaction;
        });
    }
}
