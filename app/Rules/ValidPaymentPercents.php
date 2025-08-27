<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPaymentPercents implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value) || empty($value)) {
            return;
        }

        $payers = collect($value)->filter(fn ($owner) => $owner['is_payer'] ?? false);

        $sum = $payers->sum(fn ($owner) => (float) ($owner['payment_percent'] ?? 0));

        if ($payers->count() === 1) {
            $payer = $payers->first();
            if ((float) ($payer['payment_percent'] ?? 0) !== 100.0) {
                $fail('Если плательщик один, его процент оплаты должен быть 100%.');
            }
        } else {
            if ($sum !== 100.0) {
                $fail('Сумма процентов оплаты всех плательщиков должна быть ровно 100%.');
            }
        }
    }
}
