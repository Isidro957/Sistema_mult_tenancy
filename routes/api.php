<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Middleware\ResolveTenant;

/*
|--------------------------------------------------------------------------
| API Routes - MULTI TENANT
|--------------------------------------------------------------------------
| Autenticação via Sanctum (Bearer Token)
|--------------------------------------------------------------------------
*/

/**
 * ===============================
 * LOGIN / REGISTER (GLOBAL)
 * ===============================
 */
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

/**
 * ===============================
 * ROTAS PROTEGIDAS DO TENANT
 * ===============================
 */
Route::middleware([ResolveTenant::class, 'auth:sanctum'])->group(function () {

    Route::get('/tenant-info', function () {
        return response()->json([
            'tenant' => app('tenant'),
            'user'   => auth('sactum')->user(),
        ]);
    });

    Route::apiResource('users', TenantUserController::class);
});
