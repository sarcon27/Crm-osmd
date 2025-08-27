<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreFinanceAccountDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreFinanceAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'number' => ['required', 'string'],
            'finance_owner_type' => ['required', 'string'],
            'finance_owner_id' => ['required', 'integer'],
            'finance_type' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'finance_owner_id' => 'Владелец счета',
        ];
    }

    public function getDto(): StoreFinanceAccountDto
    {
        return new StoreFinanceAccountDto(...$this->validated());
    }
}
