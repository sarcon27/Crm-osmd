<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Measure extends Model implements TenantableBootInterface
{
    use SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'name',
    ];

    public function tariffs()
    {
        return $this->hasMany(Tariff::class);
    }
}
