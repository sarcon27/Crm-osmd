<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\SearchFinanceAccountDto;
use Illuminate\Foundation\Http\FormRequest;

class SearchFinanceAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string'],
        ];
    }

    public function getDto(): SearchFinanceAccountDto
    {
        return new SearchFinanceAccountDto(...$this->validated());
    }
}
