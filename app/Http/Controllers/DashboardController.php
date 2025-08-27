<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

final class DashboardController
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Dashboard', [
            'title' => 'Статистика',
        ]);
    }
}
