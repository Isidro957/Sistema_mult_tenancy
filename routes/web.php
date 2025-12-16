<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantUserController;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Login e registro GLOBAL (LANDLORD)
Route::get('/login', [TenantAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [TenantAuthController::class, 'login']);

Route::get('/register', [TenantAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [TenantAuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| TENANT (SUBDOMÍNIO)
|--------------------------------------------------------------------------
| Ex:
| bai.localhost
| bic.localhost
*/
Route::domain('{tenant}.faturaja.sdoca')
    ->middleware(ResolveTenant::class)
    ->group(function () {

        /*
        |----------------------------------
        | ROTA DE AUTENTICAÇÃO (PÚBLICA)
        |----------------------------------
        */
        Route::get('/authenticate', function (Illuminate\Http\Request $request) {

            if (! Auth::guard('tenant')->attempt(
                $request->only('email', 'password')
            )) {
                abort(401, 'Credenciais inválidas');
            }

            $request->session()->regenerate();

            return redirect()->route('tenant.dashboard');
        });

        /*
        |----------------------------------
        | ROTAS PROTEGIDAS
        |----------------------------------
        */
        Route::middleware('auth:tenant')->group(function () {

            Route::get('/dashboard', function () {
                return view('tenant.dashboard');
            })->name('tenant.dashboard');

            Route::prefix('users')->name('tenant.users.')->group(function () {
                Route::get('/', [TenantUserController::class, 'index'])->name('index');
                Route::get('/create', [TenantUserController::class, 'create'])->name('create');
                Route::post('/', [TenantUserController::class, 'store'])->name('store');
                Route::get('/{user}/edit', [TenantUserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [TenantUserController::class, 'update'])->name('update');
                Route::delete('/{user}', [TenantUserController::class, 'destroy'])->name('destroy');
            });

            Route::post('/logout', [TenantAuthController::class, 'logout'])
                ->name('tenant.logout');
        });
    });
