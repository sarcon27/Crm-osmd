<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\FinanceOwnerTypesEnum;
use App\Models\FinanceAccount;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

readonly class ValidFinanceTransaction implements ValidationRule
{
    public function __construct(private ?int $debitAccountId, private ?int $creditAccountId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $debitAccount = FinanceAccount::find($this->debitAccountId);
        $creditAccount = FinanceAccount::find($this->creditAccountId);

        if (! $debitAccount || ! $creditAccount) {
            $fail('Неверные счёта для транзакции.');

            return;
        }

        // 1. Дебет-счет apartment: может платить только компаниям
        if ($debitAccount->finance_owner_type === FinanceOwnerTypesEnum::Apartment->value &&
            $creditAccount->finance_owner_type !== FinanceOwnerTypesEnum::Company->value
        ) {
            $fail('Счёт типа "Квартира" может платить только компании.');
        }

        // 2. На apartment может платить только компания
        if ($creditAccount->finance_owner_type === FinanceOwnerTypesEnum::Apartment->value &&
            $debitAccount->finance_owner_type !== FinanceOwnerTypesEnum::Company->value
        ) {
            $fail('На счёт типа "Квартира" может платить только компания.');
        }

        // 3. На third_party только компания
        if ($creditAccount->finance_owner_type === FinanceOwnerTypesEnum::ThirdParty->value &&
            $debitAccount->finance_owner_type !== FinanceOwnerTypesEnum::Company->value
        ) {
            $fail('На счёт типа "Third Party" может платить только компания.');
        }

        // 4. third_party не платит никому
        if ($debitAccount->finance_owner_type === FinanceOwnerTypesEnum::ThirdParty->value) {
            $fail('Счёт типа "Third Party" не может проводить платежи.');
        }

        // 5. Дебет и Кредит не могут быть одинаковыми
        if ($debitAccount->id === $creditAccount->id) {
            $fail('Дебет и Кредит не могут быть одинаковыми счетами.');
        }
    }
}
