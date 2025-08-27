<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\FinanceTransactionBuilder;
use App\Dto\InvoiceDto;
use App\Dto\InvoiceItemDto;
use App\Enums\FinanceOwnerTypesEnum;
use App\Mappers\InvoiceFromCalculationMapper;
use App\Models\Apartment;
use App\Models\FinanceAccount;
use App\Models\Invoice;
use App\Services\Factory\CalculateServices\TariffCalculatorFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class InvoiceService extends BaseService
{
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    public function search(): LengthAwarePaginator
    {
        return QueryBuilder::for($this->model::class)
            ->with([
                'items.tariff',
                'items.measure',
                'owner',
                'apartment.building',
                'apartment.financeAccount',
                'transactionEntry.transaction'])
            ->allowedFilters([
                'period',
                AllowedFilter::callback('address', function ($query, $value) {
                    $query->whereHas('apartment.building', function ($q) use ($value) {
                        $q->where('address', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('owner', function ($query, $value) {
                    $query->whereHas('owner', function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('number', function ($query, $value) {
                    $query->whereHas('apartment', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('finance_account_number', function ($query, $value) {
                    $query->whereHas('apartment.financeAccount', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    });
                }),
            ])
            ->allowedSorts([
                'total',
                'debt',
            ])
            ->defaultSort('-id')
            ->paginate(15);
    }

    public function calculate(Apartment $apartment): array
    {
        $apartment->load('building');

        $report = [];
        $servicesTotal = [];
        $totalSumPrice = 0;
        $services = $apartment->allServices();

        foreach ($services as $service) {
            $tariff = $service->tariff()->active()->first();

            if (! $tariff) {
                continue;
            }

            $calculateData = TariffCalculatorFactory::calculate($tariff, $apartment);

            $totalSumPrice += $calculateData->amount;

            $servicesTotal[] = [
                'service' => $service,
                'tariff' => $tariff,
                'sum' => $calculateData->amount,
                'quantity' => $calculateData->quantity,
            ];
        }

        foreach ($apartment->owners as $owner) {
            if (empty((float) $owner->pivot->payment_percent)) {
                continue;
            }

            $ownerReport = [];
            $ownerTotal = 0;

            foreach ($servicesTotal as $serviceData) {
                $ownerShare = $serviceData['sum'] * ($owner->pivot->payment_percent / 100);
                $ownerTotal += $ownerShare;

                $ownerReport[] = array_merge($serviceData, [
                    'owner_share' => round($ownerShare, 2),
                ]);
            }

            $report['data'][$owner->id]['services'] = $ownerReport;
            $report['data'][$owner->id]['ownerTotal'] = round($ownerTotal, 2);
            $report['data'][$owner->id]['totalSumPrice'] = round($totalSumPrice, 2);
            $report['data'][$owner->id]['owner'] = $owner;
            $report['data'][$owner->id]['percent'] = $owner->pivot->payment_percent;
            $report['data'][$owner->id]['apartment'] = $apartment;

        }

        $report['totalSumPrice'] = round($totalSumPrice, 2);

        return $report;

    }

    public function createInvoicesFromCalculation(DataTransferObject $dto): void
    {
        $apartment = Apartment::query()->findOrFail($dto->apartment_id);

        $data = $this->calculate($apartment);

        $companyFinanceAccount = FinanceAccount::query()->where('finance_owner_type', FinanceOwnerTypesEnum::Company)->first();

        $transaction = FinanceTransactionBuilder::builder()
            ->name('Оплата коммунальных услуг за '.now()->format('Y-m'))
            ->debitAccount($companyFinanceAccount->id)
            ->creditAccount($apartment->financeAccount->id)
            ->total($data['totalSumPrice'] ?? 0)
            ->save();

        foreach ($data['data'] as $ownerId => $ownerData) {
            $entry = $transaction->entries()->create([
                'amount' => $ownerData['ownerTotal'],
                'notes' => $ownerData['owner']->name,
            ]);

            $invoiceDto = new InvoiceDto([
                'apartment_id' => $ownerData['apartment']->id,
                'apartment_owner_id' => $ownerId,
                'period' => now()->format('Y-m'),
                'total' => $ownerData['ownerTotal'],
                'debt' => 0,
                'initial_finance_transaction_entry_id' => $entry->id,
                'items' => array_map(fn ($service) => new InvoiceItemDto([
                    'service_id' => $service['service']->id,
                    'tariff_id' => $service['tariff']->id,
                    'measure_id' => $service['tariff']->measure_id,
                    'quantity' => $service['quantity'],
                    'amount' => $service['sum'],
                ]), $ownerData['services']),
            ]);

            $mapper = new InvoiceFromCalculationMapper;
            $mappedData = $mapper->map($invoiceDto);

            Invoice::query()->create($mappedData['invoice'])->items()->createMany($mappedData['items']);
        }

    }
}
