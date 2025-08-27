<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinanceTransactionRequest;
use App\Http\Resources\Dashboard\FinanceTransactionResources\FinanceTransactionResourceCollection;
use App\Models\FinanceTransaction;
use App\Services\FinanceTransactionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class FinanceTransactionController extends Controller
{
    public function __construct(private readonly FinanceTransactionService $service) {}

    public function index(): Response
    {
        return Inertia::render('FinanceTransaction/Index', [
            'title' => 'Проводки',
            'transactions' => new FinanceTransactionResourceCollection($this->service->search()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreFinanceTransactionRequest $request): RedirectResponse
    {
        $this->service->createFromBuilder($request->getDto());

        return back();
    }

    public function post(FinanceTransaction $transaction): RedirectResponse
    {
        $this->service->post($transaction);

        return back();
    }

    public function reject(FinanceTransaction $transaction): RedirectResponse
    {
        $this->service->reject($transaction);

        return back();
    }
}
