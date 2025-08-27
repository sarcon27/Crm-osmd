<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceTransactionStatusEnum;
use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTransaction extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'status',
        'name',
        'debit_account_id',
        'credit_account_id',
        'posted_at',
        'total',
    ];

    protected $casts = [
        'status' => FinanceTransactionStatusEnum::class,
        'total' => 'decimal:2',
        'posted_at' => 'datetime',

    ];

    public function debit_account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'debit_account_id');
    }

    public function credit_account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'credit_account_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(FinanceTransactionEntry::class);
    }
}
