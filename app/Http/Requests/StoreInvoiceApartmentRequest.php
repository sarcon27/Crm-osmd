<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreInvoiceApartmentDto;
use App\Rules\InvoiceUniqueForApartmentAndPeriod;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceApartmentRequest extends FormRequest
{
    public function rules(): array
    {
        $period = now()->format('Y-m'); // Возможно нужно будет иметь возможность выбирать период

        return [
            'apartment_id' => ['required', 'integer', 'exists:apartments,id', new InvoiceUniqueForApartmentAndPeriod($this->apartment_id, $period)],

        ];
    }

    public function attributes(): array
    {
        return [
            'apartment_id' => 'Квартира',

        ];
    }

    public function getDto(): StoreInvoiceApartmentDto
    {
        return new StoreInvoiceApartmentDto(...$this->validated());
    }
}
