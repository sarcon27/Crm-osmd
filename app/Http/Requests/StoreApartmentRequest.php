<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\StoreApartmentDto;
use App\Enums\AppartamentStatusEnum;
use App\Enums\AppartamentTypeEnum;
use App\Rules\AtLeastOnePayer;
use App\Rules\FloorDoesNotExceedSectionFloors;
use App\Rules\ValidPaymentPercents;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApartmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'section_id' => ['required', 'integer'],
            'building_id' => ['required', 'integer'],
            'number' => ['required', 'string'],
            'registered_residents' => ['required', 'integer', 'min:1'],
            'floor' => ['required', 'integer', 'min:1', new FloorDoesNotExceedSectionFloors],
            'total_area' => ['required', 'numeric'],
            'living_area' => ['required', 'numeric'],
            'rooms_count' => ['required', 'integer'],
            'status' => ['required', 'string', Rule::enum(AppartamentStatusEnum::class)],
            'type' => ['required', 'string', Rule::enum(AppartamentTypeEnum::class)],
            'notes' => ['nullable', 'string'],
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'owners' => ['nullable', 'array', new AtLeastOnePayer, new ValidPaymentPercents],
            'owners.*.id' => ['nullable', 'integer'],
            'owners.*.name' => ['required', 'string'],
            'owners.*.phone' => ['nullable', 'string'],
            'owners.*.email' => ['nullable', 'string', 'email'],
            'owners.*.is_payer' => ['nullable', 'boolean'],
            'owners.*.payment_percent' => ['nullable', 'numeric'],
            'extraServices' => ['nullable', 'array'],

        ];
    }

    public function attributes(): array
    {
        return [
            'building_id' => 'Дом',
            'section_id' => 'Секция',
            'floor' => 'Этаж',
            'area' => 'Площадь',
            'rooms_count' => 'Кол-во комнат',
            'type' => 'Тип жилья',
            'status' => 'Статус',
            'registered_residents' => 'Кол-во прописанных',
            'owners.*.name' => 'ФИО',
            'owners.*.phone' => 'Телефон',
            'owners.*.email' => 'Email',
            'owners.*.payment_percent' => 'Процент оплаты',
            'extraServices' => 'Доп. услуги',

        ];
    }

    public function getDto(): StoreApartmentDto
    {
        return new StoreApartmentDto(...$this->validated());
    }
}
