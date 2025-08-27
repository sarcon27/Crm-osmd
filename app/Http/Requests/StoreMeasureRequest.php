<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreMeasureDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeasureRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Ед. измерения',
            'price' => 'Стоимость',
        ];
    }

    public function getDto(): StoreMeasureDto
    {
        return new StoreMeasureDto(...$this->validated());
    }
}
