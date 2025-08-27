<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTransactionEntry extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $table = 'finance_transaction_entries';

    protected $fillable = [
        'tenant_id',
        'finance_transaction_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinanceTransaction::class, 'finance_transaction_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'initial_finance_transaction_entry_id');
    }
}
