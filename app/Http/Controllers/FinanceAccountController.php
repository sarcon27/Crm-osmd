<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchFinanceAccountRequest;
use App\Http\Requests\StoreFinanceAccountRequest;
use App\Http\Resources\Dashboard\FinanceAccountResources\FinanceAccountResourceCollection;
use App\Services\FinanceAccountService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class FinanceAccountController extends Controller
{
    public function __construct(private readonly FinanceAccountService $service) {}

    public function index(): Response
    {
        return Inertia::render('FinanceAccount/Index', [
            'title' => 'Счета',
            // 'apartmentAccounts' => new FinanceAccountResourceCollection($this->service->search()),
            'mainAccounts' => new FinanceAccountResourceCollection($this->service->getMainAccounts()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreFinanceAccountRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    public function search(SearchFinanceAccountRequest $request): FinanceAccountResourceCollection
    {
        $result = $this->service->searchForAutocomplite($request->getDto());

        return new FinanceAccountResourceCollection($result);
    }
}
