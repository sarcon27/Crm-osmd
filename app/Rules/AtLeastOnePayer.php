<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AtLeastOnePayer implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $hasPayer = collect($value)->contains(fn ($owner) => ! empty($owner['is_payer']));

        if (! $hasPayer) {
            $fail('Должен быть выбран хотя бы один плательщик.');
        }
    }
}
