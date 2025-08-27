<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchOwnerRequest;
use App\Http\Requests\StoreOwnerRequest;
use App\Http\Resources\Dashboard\OwnerResources\OwnerResourceCollection;
use App\Services\OwnerService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class OwnerController extends Controller
{
    public function __construct(private readonly OwnerService $service) {}

    public function index(): Response
    {
        return Inertia::render('Owner/Index', [
            'title' => 'Жители',
            'owners' => new OwnerResourceCollection($this->service->search()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreOwnerRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    /**
     * @throws \Exception
     */
    public function update(StoreOwnerRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->getDto());

        return back();
    }

    public function search(SearchOwnerRequest $request): OwnerResourceCollection
    {
        $result = $this->service->searchForAutocomplite($request->getDto());

        return new OwnerResourceCollection($result);

    }
}
