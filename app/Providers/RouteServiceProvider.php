<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */

public function boot(): void
{
    parent::boot();

    // CLIENTES (INT)
    Route::bind('client', function ($value) {
        return Cliente::where('tenant_id', app('tenant')->id)
            ->where('id', $value)
            ->firstOrFail();
    });

    // USERS (UUID)
    Route::bind('user', function ($value) {
        return TenantUser::where('tenant_id', app('tenant')->id)
            ->where('id', $value)
            ->firstOrFail();
    });
}
}