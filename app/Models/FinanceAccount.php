<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceOwnerTypesEnum;
use App\Enums\FinanceTypesEnum;
use App\Services\Interfaces\TenantableBootInterface;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

final class FinanceAccount extends Model implements TenantableBootInterface
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = [
        'tenant_id',
        'finance_owner_id',
        'finance_owner_type',
        'finance_type',
        'number',
        'name',
        'debit',
        'credit',
        'balance',
    ];

    protected $casts = [
        'finance_owner_type' => FinanceOwnerTypesEnum::class,
        'type' => FinanceTypesEnum::class,
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        Relation::enforceMorphMap([
            'apartment' => Apartment::class,
            'company' => Company::class,
            'third_party' => ThirdParty::class,
        ]);
    }

    public function financeOwner(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'finance_owner_type', 'finance_owner_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceTransactionEntry::class, 'finance_account_id');
    }

    public function scopeMain(Builder $query): void
    {
        $query->whereIn('finance_owner_type', [FinanceOwnerTypesEnum::ThirdParty, FinanceOwnerTypesEnum::Company]);
    }

    public function scopeApartments(Builder $query): void
    {
        $query->where('finance_owner_type', FinanceOwnerTypesEnum::Apartment);
    }

    public function scopeCompany(Builder $query): void
    {
        $query->where('finance_owner_type', FinanceOwnerTypesEnum::Company);
    }

    public function scopeThirdParty(Builder $query): void
    {
        $query->where('finance_owner_type', FinanceOwnerTypesEnum::ThirdParty);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('finance_type', FinanceTypesEnum::Active);
    }

    public function scopePassive(Builder $query): void
    {
        $query->where('finance_type', FinanceTypesEnum::Passive);
    }
}
