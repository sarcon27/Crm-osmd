<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Dashboard\InvoiceResources\InvoiceResourceCollection;
use App\Models\Apartment;
use App\Services\InvoiceService;
use Inertia\Inertia;
use Inertia\Response;

final class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $service) {}

    public function index(): Response
    {
        return Inertia::render('Invoice/Index', [
            'title' => 'Документы',
            'invoices' => new InvoiceResourceCollection($this->service->search()),
            'filters' => request()->all(),
        ]);
    }

    public function calculate(Apartment $apartment): array
    {
        return $this->service->calculate($apartment);
    }
}
