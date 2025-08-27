<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreMeterDto;
use App\Rules\UniqueMeterPeriod;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'apartment_id' => ['required', 'integer', 'exists:apartments,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'new_value' => ['required', 'numeric', 'min:0',  new UniqueMeterPeriod($this->apartment_id, $this->service_id, $this->is_next_period)],
            'is_next_period' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'apartment_id' => 'Квартира',
            'service_id' => 'Услуга',
            'new_value' => 'Показание',
        ];
    }

    public function getDto(): StoreMeterDto
    {
        return new StoreMeterDto(...$this->validated());
    }
}
