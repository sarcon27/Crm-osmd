<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppartamentStatusEnum;
use App\Enums\AppartamentTypeEnum;
use App\Enums\FinanceOwnerTypesEnum;
use App\Enums\TariffTypeEnum;
use App\Events\ApartmentCreatedEvent;
use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartment extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'building_id',
        'section_id',
        'number',
        'floor',
        'total_area',
        'living_area',
        'rooms_count',
        'status',
        'type',
        'notes',
        'registered_residents',
    ];

    protected $casts = [
        'status' => AppartamentStatusEnum::class,
        'type' => AppartamentTypeEnum::class,
        'rooms_count' => 'integer',
        'number' => 'string',
        'floor' => 'integer',
        'total_area' => 'float',
        'living_area' => 'float',
        'registered_residents' => 'integer',

    ];

    protected $dispatchesEvents = [
        'created' => ApartmentCreatedEvent::class,
    ];

    public function financeAccount(): HasOne
    {
        return $this->hasOne(FinanceAccount::class, 'finance_owner_id')
            ->where('finance_owner_type', FinanceOwnerTypesEnum::Apartment->value);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Owner::class, 'apartment_owner')
            ->withPivot(['is_payer', 'payment_percent'])
            ->withTimestamps();
    }

    public function extraServices(): MorphToMany
    {
        return $this->morphToMany(Service::class, 'serviceable')->with('tariff')
            ->withTimestamps();
    }

    public function servicesWithMeter(): Collection
    {
        return $this->allServices()->filter(fn ($service) => $service->tariff && $service->tariff->type === TariffTypeEnum::Meter);
    }

    // Получаем все услуги квартиры (базовые + доп + ??исключения базовых)
    public function allServices(): Collection
    {
        $baseServices = $this->building->services ?? collect();
        $baseServices->load('tariff');
        $extraServices = $this->extraServices ?? collect();

        // Обдумываем, нужно ли это
        //        $exceptions = $this->serviceExceptions->pluck('service_id')->toArray();
        //        $baseServices = $baseServices->reject(fn ($s) => in_array($s->id, $exceptions));

        return $baseServices->merge($extraServices);
    }

    //    public function serviceExceptions()
    //    {
    //        return $this->hasMany(ApartmentServiceException::class);
    //    }

    /**
     * Синхронизирует владельцев квартиры
     *
     * @param  array  $owners  Массив объектов StoreOwnerDto
     */
    public function syncOwners(array $owners): void
    {
        if (empty($owners)) {
            $this->owners()->detach();

            return;
        }

        if (! collect($owners)->contains(fn ($o) => $o->is_payer ?? false)) {
            throw new Exception('Должен быть выбран хотя бы один плательщик!');
        }

        $syncData = [];

        foreach ($owners as $ownerDto) {
            if (! isset($ownerDto->id)) {
                $owner = Owner::create([
                    'name' => $ownerDto->name,
                    'phone' => $ownerDto->phone ?? null,
                    'email' => $ownerDto->email ?? null,
                ]);
                $ownerId = $owner->id;
            } else {
                $ownerId = $ownerDto->id;
            }

            $syncData[$ownerId] = [
                'is_payer' => $ownerDto->is_payer ?? false,
                'payment_percent' => ($ownerDto->is_payer ?? false) ? ($ownerDto->payment_percent ?? 0) : 0, // обнуляем
            ];
        }

        $this->owners()->sync($syncData);
    }
}
