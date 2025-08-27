<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Meter;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueMeterPeriod implements ValidationRule
{
    public function __construct(private readonly ?int $apartmentId, private readonly ?int $serviceId, private readonly bool $isNextPeriod) {}

    /**
     * @param  \Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $period = $this->isNextPeriod
            ? Carbon::now()->addMonth()->format('Y-m')
            : Carbon::now()->format('Y-m');

        $exists = Meter::query()
            ->where('apartment_id', $this->apartmentId)
            ->where('service_id', $this->serviceId)
            ->where('period', $period)
            ->exists();

        if ($exists) {
            $fail('Показания за этот период уже были внесены.');
        }
    }
}
