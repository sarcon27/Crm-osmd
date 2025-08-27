<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes , TenantTrait;

    protected $table = 'invoice_items';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'service_id',
        'tariff_id',
        'quantity',
        'measure_id',
        'amount',
        'finance_transaction_entry_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }

    public function transactionEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceTransactionEntry::class, 'finance_transaction_entry_id');
    }
}
