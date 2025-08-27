<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $service) {}

    public function index(): Response
    {
        return Inertia::render('Setting/Index', [
            'title' => 'Настройки',
            'settings' => $this->service->get(),
        ]);
    }

    public function save(StoreSettingRequest $request): RedirectResponse
    {
        $this->service->save($request->getDto());

        return back();
    }
}
