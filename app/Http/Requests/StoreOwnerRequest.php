<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreOwnerDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'string', 'email'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'фИО',
            'phone' => 'Телефон',
            'email' => 'Email',
            'notes' => 'Заметки',

        ];
    }

    public function getDto(): StoreOwnerDto
    {
        return new StoreOwnerDto(...$this->validated());
    }
}
