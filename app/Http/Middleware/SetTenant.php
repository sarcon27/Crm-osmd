<?php

namespace App\Http\Middleware;

use Closure;
use Rawilk\Settings\Facades\Settings;

class SetTenant
{
    public function handle($request, Closure $next)
    {
        if ($user = $request->user()) {
            if ($user->tenant) {
                $user->tenant->makeCurrent();
                Settings::setTeamId($user->tenant->id);
            }

        }

        return $next($request);
    }
}
