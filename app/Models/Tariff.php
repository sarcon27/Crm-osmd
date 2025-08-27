<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TariffStatusEnum;
use App\Enums\TariffTypeEnum;
use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tariff extends Model implements TenantableBootInterface
{
    use SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'name',
        'price',
        'measure_id',
        'service_id',
        'status',
        'type',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => TariffStatusEnum::class,
        'type' => TariffTypeEnum::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected function startDate(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (! $value) {
                    return null;
                }

                return Carbon::parse($value, config('app.timezone'))->setTimezone(config('app.timezone'));
            },
            get: function ($value) {
                if (! $value) {
                    return null;
                }

                return Carbon::parse($value, config('app.timezone'))->format('Y-m-d');
            }
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (! $value) {
                    return null;
                }

                return Carbon::parse($value, config('app.timezone'))->setTimezone(config('app.timezone'));
            },
            get: function ($value) {
                if (! $value) {
                    return null;
                }

                return Carbon::parse($value, config('app.timezone'))->format('Y-m-d');
            }
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class)->withTrashed();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', TariffStatusEnum::Active);
    }
}
