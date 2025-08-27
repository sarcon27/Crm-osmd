<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreFinanceTransactionDto;
use App\Rules\ValidFinanceTransaction;
use Illuminate\Foundation\Http\FormRequest;

class StoreFinanceTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'debit_account_id' => ['required', 'integer', 'exists:finance_accounts,id'],
            'credit_account_id' => ['required', 'integer', 'exists:finance_accounts,id', 'different:debit_account_id'],
            'status' => ['nullable', 'string'],
            'name' => ['required', 'string'],
            'total' => ['required', 'numeric', 'min:0.01',
                new ValidFinanceTransaction($this->debit_account_id, $this->credit_account_id),
            ],

        ];
    }

    public function attributes(): array
    {
        return [
            'debit_account_id' => 'Дебит',
            'credit_account_id' => 'Кредит',
            'total' => 'Сумма',
            'name' => 'Название',

        ];
    }

    public function getDto(): StoreFinanceTransactionDto
    {
        return new StoreFinanceTransactionDto(...$this->validated());
    }
}
