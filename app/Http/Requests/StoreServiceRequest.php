<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreServiceDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'is_auto' => ['required', 'boolean'],
            'is_base' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Название',
            'notes' => 'Заметки',
        ];
    }

    public function getDto(): StoreServiceDto
    {
        return new StoreServiceDto(...$this->validated());
    }
}
