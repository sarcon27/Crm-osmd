<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class OwnerUniqueForApartment implements ValidationRule
{
    protected int $apartmentId;

    public function __construct(int $apartmentId)
    {
        $this->apartmentId = $apartmentId;
    }

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

        foreach ($value as $owner) {
            $exists = DB::table('apartment_owner')
                ->where('apartment_id', $this->apartmentId)
                ->where('owner_id', $owner['id'])
                ->exists();

            if ($exists) {
                $fail("Владелец {$owner['name']} уже принадлежит этой квартире.");
            }
        }
    }
}
