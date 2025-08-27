<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface TenantableBootInterface
{
    public static function bootTenantTrait(): void;

    public function ensureTenantId(): void;

    public function tenant(): BelongsTo;
}
