<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TariffStatusEnum;
use App\Enums\TariffTypeEnum;
use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model implements TenantableBootInterface
{
    use SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'name',
        'note',
        'is_base',
        'is_auto',
    ];

    protected $casts = [
        'is_auto' => 'boolean',
        'is_base' => 'boolean',
    ];

    public function buildings()
    {
        return $this->morphedByMany(Building::class, 'serviceable')
            ->withTimestamps();
    }

    public function apartments()
    {
        return $this->morphedByMany(Apartment::class, 'serviceable')
            ->withTimestamps();
    }

    public function tariff(): HasOne
    {
        return $this->hasOne(Tariff::class)->where('status', TariffStatusEnum::Active)->latest('id');
    }

    public function scopeBasic(Builder $query): void
    {
        $query->where('is_base', true);

    }

    public function scopeExtra(Builder $query): void
    {
        $query->where('is_base', false);

    }

    public function scopeByTariffType(Builder $query, TariffTypeEnum $type): Builder
    {
        return $query->whereHas('tariff', function ($q) use ($type) {
            $q->where('type', $type);
        });
    }
}
