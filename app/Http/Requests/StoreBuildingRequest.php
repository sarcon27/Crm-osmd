<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreBuildingDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.name' => ['required', 'string', 'max:255'],
            'sections.*.floors_count' => ['required', 'integer', 'min:1', 'max:50'],
            'services' => ['required', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Название',
            'address' => 'Адрес',
            'description' => 'Описание',
            'services' => 'Базовые слуги',
        ];
    }

    public function messages(): array
    {
        return [
            'sections.required' => 'Добавьте хотя бы одну секцию',
            'sections.*.name.required' => 'Название секции обязательно',
            'sections.*.floors_count.required' => 'Укажите количество этажей',
        ];
    }

    public function getDto(): StoreBuildingDto
    {
        return new StoreBuildingDto(...$this->validated());
    }
}
