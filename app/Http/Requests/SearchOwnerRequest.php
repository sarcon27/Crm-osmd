<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\SearchOwnerDto;
use Illuminate\Foundation\Http\FormRequest;

class SearchOwnerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string'],
        ];
    }

    public function getDto(): SearchOwnerDto
    {
        return new SearchOwnerDto(...$this->validated());
    }
}
