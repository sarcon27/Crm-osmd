<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'notes',
    ];

    public function apartments(): BelongsToMany
    {
        return $this->belongsToMany(Apartment::class, 'apartment_owner')
            ->withPivot(['is_payer', 'payment_percent']);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
