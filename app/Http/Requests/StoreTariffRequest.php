<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreTariffDto;
use App\Enums\TariffStatusEnum;
use App\Enums\TariffTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTariffRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'string'],
            'end_date' => ['nullable', 'string'],
            'measure_id' => ['required', 'integer', 'exists:measures,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::enum(TariffStatusEnum::class)],
            'type' => ['required', 'string', Rule::enum(TariffTypeEnum::class)],
            'notes' => ['nullable', 'string'],

        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Название',
            'price' => 'Стоимость',
            'measure_id' => 'Ед. измерения',

        ];
    }

    public function getDto(): StoreTariffDto
    {
        return new StoreTariffDto(...$this->validated());
    }
}
