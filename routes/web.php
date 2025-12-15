<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantUserController;
use App\Http\Middleware\ResolveTenant;

/*
|--------------------------------------------------------------------------
| LANDLORD
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/tenants', [TenantController::class, 'store'])
    ->name('tenants.store');

/*
|--------------------------------------------------------------------------
| TENANT (SUBDOMÍNIO)
|--------------------------------------------------------------------------
*/

Route::prefix('tenant')
    ->middleware(ResolveTenant::class)
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | AUTH DO TENANT (PÚBLICO)
        |--------------------------------------------------------------------------
        */

        Route::get('/login', [TenantAuthController::class, 'showLoginForm'])
            ->name('tenant.login');

        Route::post('/login', [TenantAuthController::class, 'login']);

        Route::get('/register', [TenantAuthController::class, 'showRegisterForm'])
            ->name('tenant.register');

        Route::post('/register', [TenantAuthController::class, 'register']);

        Route::post('/logout', [TenantAuthController::class, 'logout'])
            ->middleware('auth:tenant') // CORRETO: usar auth:tenant
            ->name('tenant.logout');

        /*
        |--------------------------------------------------------------------------
        | ROTAS PROTEGIDAS DO TENANT
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:tenant')->group(function () {

            // Dashboard
            Route::get('/dashboard', function () {
                return view('tenant.dashboard');
            })->name('tenant.dashboard');

            // CRUD de usuários do tenant
            Route::prefix('users')
                ->name('tenant.users.')
                ->group(function () {

                    Route::get('/', [TenantUserController::class, 'index'])->name('index');
                    Route::get('/create', [TenantUserController::class, 'create'])->name('create');
                    Route::post('/', [TenantUserController::class, 'store'])->name('store');
                    Route::get('/{user}/edit', [TenantUserController::class, 'edit'])->name('edit');
                    Route::put('/{user}', [TenantUserController::class, 'update'])->name('update');
                    Route::delete('/{user}', [TenantUserController::class, 'destroy'])->name('destroy');
                });
        });
    });
