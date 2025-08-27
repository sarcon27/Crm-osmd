<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meter extends Model implements TenantableBootInterface
{
    use TenantTrait;

    protected $fillable = [
        'tenant_id',
        'apartment_id',
        'service_id',
        'new_value',
        'old_value',
        'period',
    ];

    protected $casts = [
        'new_value' => 'decimal:2',
        'old_value' => 'decimal:2',

    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
