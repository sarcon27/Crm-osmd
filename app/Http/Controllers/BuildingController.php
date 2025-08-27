<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingRequest;
use App\Http\Resources\Dashboard\BuildingResources\BuildingResourceCollection;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResourceCollection;
use App\Services\BuildingService;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class BuildingController extends Controller
{
    public function __construct(private readonly BuildingService $service, private readonly ServiceService $serviceService) {}

    public function index(): Response
    {
        return Inertia::render('Building/Index', [
            'title' => 'Дома',
            'buildings' => new BuildingResourceCollection($this->service->search()),
            'basicServices' => new ServiceResourceCollection($this->serviceService->getBasicServices()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    /**
     * @throws \Exception
     */
    public function update(StoreBuildingRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->getDto());

        return back();
    }
}
