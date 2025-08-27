<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTariffRequest;
use App\Services\TariffService;
use Illuminate\Http\RedirectResponse;

final class TariffController extends Controller
{
    public function __construct(private readonly TariffService $service) {}

    /**
     * @throws \Exception
     */
    public function store(StoreTariffRequest $request): RedirectResponse
    {
        $this->service->create($request->getDto());

        return back();
    }

    /**
     * @throws \Exception
     */
    public function update(StoreTariffRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->getDto());

        return back();
    }
}
