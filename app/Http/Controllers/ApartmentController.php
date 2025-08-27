<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\StoreInvoiceApartmentRequest;
use App\Http\Resources\Dashboard\ApartmentResources\ApartmentResourceCollection;
use App\Http\Resources\Dashboard\BuildingResources\BuildingResourceCollection;
use App\Http\Resources\Dashboard\ServiceResources\ServiceResourceCollection;
use App\Models\Apartment;
use App\Services\ApartmentService;
use App\Services\BuildingService;
use App\Services\InvoiceService;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ApartmentController extends Controller
{
    public function __construct(
        private readonly ApartmentService $service,
        private readonly BuildingService $buildingService,
        private readonly InvoiceService $invoiceService,
        private readonly ServiceService $serviceService) {}

    public function index(): Response
    {
        return Inertia::render('Apartment/Index', [
            'title' => 'Квартиры',
            'apartments' => new ApartmentResourceCollection($this->service->search()),
            'buildings' => new BuildingResourceCollection($this->buildingService->allWithSections()),
            'extraServices' => new ServiceResourceCollection($this->serviceService->getExtraServices()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreApartmentRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    /**
     * @throws \Exception
     */
    public function update(StoreApartmentRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->getDto());

        return back();
    }

    public function getMeterServices(Apartment $apartment)
    {
        $services = $this->service->getServicesWithMeter($apartment);

        return new ServiceResourceCollection($services);
    }

    public function createFromInvoice(StoreInvoiceApartmentRequest $request): RedirectResponse
    {
        $this->invoiceService->createInvoicesFromCalculation($request->getDto());

        return back();

    }
}
