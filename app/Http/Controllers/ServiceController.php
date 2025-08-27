<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Resources\Dashboard\MeasureResources\MeasureResourceCollection;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResourceCollection;
use App\Http\Resources\Dashboard\TariffResources\TariffResourceCollection;
use App\Services\MeasureService;
use App\Services\ServiceService;
use App\Services\TariffService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $service,
        private readonly TariffService $tariffService,
        private readonly MeasureService $measureService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Service/Index', [
            'title' => 'Управление услугами',
            'services' => new ServiceResourceCollection($this->service->all()),
            'tariffs' => new TariffResourceCollection($this->tariffService->search()),
            'measures' => new MeasureResourceCollection($this->measureService->all()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    /**
     * @throws \Exception
     */
    public function update(StoreServiceRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->getDto());

        return back();
    }
}
