<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class InvoiceUniqueForApartmentAndPeriod implements ValidationRule
{
    protected ?int $apartmentId;

    protected string $period;

    public function __construct(?int $apartmentId, string $period)
    {
        $this->apartmentId = $apartmentId;
        $this->period = $period;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('invoices')
            ->where('apartment_id', $this->apartmentId)
            ->where('period', $this->period)
            ->exists();

        if ($exists) {
            $fail("Документ для квартиры ID {$this->apartmentId} за период {$this->period} уже существует.");
        }
    }
}
