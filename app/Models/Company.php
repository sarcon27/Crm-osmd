<?php

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = ['tenant_id', 'name'];
}
