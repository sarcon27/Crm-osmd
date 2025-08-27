<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreSettingDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'key' => ['required', 'string'],
            'value' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Параметр',
            'value' => 'Значение',
        ];
    }

    public function getDto(): StoreSettingDto
    {
        return new StoreSettingDto(...$this->validated());
    }
}
