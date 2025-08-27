<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $table = 'invoices';

    protected $fillable = [
        'tenant_id',
        'apartment_id',
        'apartment_owner_id',
        'period',
        'total',
        'debt',
        'initial_finance_transaction_entry_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'debt' => 'decimal:2',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'apartment_owner_id');
    }

    public function transactionEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceTransactionEntry::class, 'initial_finance_transaction_entry_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
