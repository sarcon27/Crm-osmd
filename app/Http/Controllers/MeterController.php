<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeterRequest;
use App\Http\Resources\Dashboard\MeterResources\MeterResourceCollection;
use App\Services\MeterService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class MeterController extends Controller
{
    public function __construct(private readonly MeterService $service) {}

    public function index(): Response
    {
        return Inertia::render('Meter/Index', [
            'title' => 'Показания счетчиков',
            'meters' => new MeterResourceCollection($this->service->search()),
            'filters' => request()->all(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreMeterRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }
}
