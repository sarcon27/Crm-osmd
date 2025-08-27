<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TenantTrait
{
    public static function bootTenantTrait(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            $model->ensureTenantId();
        });
    }

    public function ensureTenantId(): void
    {
        if (isset($this->tenant_id)) {
            return;
        }

        if ($tenant = Tenant::current()) {
            $this->tenant_id = $tenant->id;
        } else {
            throw new \RuntimeException('No current tenant identified');
        }
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
