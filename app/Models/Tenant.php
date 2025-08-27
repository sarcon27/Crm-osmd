<?php

declare(strict_types=1);

namespace App\Models;

class Tenant extends \Spatie\Multitenancy\Models\Tenant
{
    protected $fillable = ['name'];
}
