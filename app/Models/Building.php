<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'description',

    ];

    public function services(): MorphToMany
    {
        return $this->morphToMany(Service::class, 'serviceable')
            ->withTimestamps();
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
