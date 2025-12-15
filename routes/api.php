<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantUserController;
use App\Http\Middleware\ResolveTenant;

/*
|--------------------------------------------------------------------------
| API Routes - MULTI TENANT
|--------------------------------------------------------------------------
| Todas as rotas passam pelo ResolveTenant.
| Sanctum é usado para autenticação via token Bearer.
|--------------------------------------------------------------------------
*/

// --------------------------------------------------------------------------
// AUTENTICAÇÃO DO TENANT (usuários da empresa)
// --------------------------------------------------------------------------
Route::prefix('auth')
    ->middleware(ResolveTenant::class) // Aplica tenant dinâmico
    ->group(function () {

        // Registro e login (públicos)
        Route::post('/register', [TenantAuthController::class, 'register']);
        Route::post('/login', [TenantAuthController::class, 'login']);

        // Logout (protegido via token Sanctum)
        Route::middleware('auth:sanctum')
            ->post('/logout', [TenantAuthController::class, 'logout']);
    });

// --------------------------------------------------------------------------
// ROTAS PROTEGIDAS DO TENANT (usuário autenticado)
// --------------------------------------------------------------------------
Route::middleware([ResolveTenant::class, 'auth:sanctum'])->group(function () {

    // Informações do tenant e do usuário autenticado
    Route::get('/tenant-info', function () {
        return response()->json([
            'tenant' => app('tenant'),          // tenant atual
            'user'   => auth('sanctum')->user(), // usuário autenticado
        ]);
    });

    // CRUD de usuários do tenant
    Route::prefix('users')->group(function () {
        Route::get('/', [TenantUserController::class, 'index']);        // Listar usuários
        Route::get('/{user}', [TenantUserController::class, 'show']);   // Detalhes do usuário
        Route::post('/', [TenantUserController::class, 'store']);       // Criar usuário
        Route::put('/{user}', [TenantUserController::class, 'update']); // Atualizar usuário
        Route::delete('/{user}', [TenantUserController::class, 'destroy']); // Deletar usuário
    });
});
