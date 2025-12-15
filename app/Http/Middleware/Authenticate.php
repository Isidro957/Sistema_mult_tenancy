<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            //  Tenant
            if ($request->is('tenant/*')) {
                return route('tenant.login');
            }

            //  Dashboard raiz (landlord ou erro comum)
            if ($request->is('dashboard')) {
                return route('tenant.login');
             }

            return '/';
        }
    }
}
