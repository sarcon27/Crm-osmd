<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeasureRequest;
use App\Services\MeasureService;
use Illuminate\Http\RedirectResponse;

final class MeasureController extends Controller
{
    public function __construct(private readonly MeasureService $service) {}

    /**
     * @throws \Exception
     */
    public function store(StoreMeasureRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    /**
     * @throws \Exception
     */
    public function update(StoreMeasureRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->getDto());

        return back();
    }
}
