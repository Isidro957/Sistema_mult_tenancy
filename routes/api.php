<?php

use App\Http\Middleware\ResolveTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;

/**
 * LOGIN GLOBAL
 */
Route::post('/login', [\App\Http\Controllers\ApiAuthController::class, 'login']);

/**
 * ROTAS DO TENANT (multi-tenant)
 */
Route::middleware([ResolveTenant::class])->group(function () {

    // Registro opcional
    Route::post('/register', [\App\Http\Controllers\ApiAuthController::class, 'register']);

    // Rotas protegidas para usuários do tenant
    Route::middleware(['auth:sanctum', 'tenant.user'])->group(function () {

        // Informações do tenant e usuário logado
        Route::get('/tenant-info', function (Request $request) {
            return response()->json([
                'tenant' => app('tenant'),
                'user'   => $request->user(),
            ]);
        });

        // Logout
        Route::post('/logout', [\App\Http\Controllers\ApiAuthController::class, 'logout']);

        // CRUD de usuários
        Route::apiResource('/users', \App\Http\Controllers\TenantUserController::class);

        // CRUD de clientes
        Route::apiResource('/clientes', ClienteController::class);

    });

});
