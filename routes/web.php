<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\ClienteWebController;
use App\Http\Middleware\ResolveTenant;

/*
|--------------------------------------------------------------------------
| LANDLORD (LOGIN GLOBAL)
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {
    Route::get('/login', [TenantAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [TenantAuthController::class, 'login']);

    Route::get('/register', [TenantAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [TenantAuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| VERIFICAÇÃO DE EMAIL
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:tenant'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $user = \App\Models\TenantUser::findOrFail($request->route('id'));
        Auth::guard('tenant')->login($user);
        $request->fulfill();
        return redirect()->route('tenant.dashboard');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user('tenant')->sendEmailVerificationNotification();
        return back()->with('success', 'Link de verificação enviado!');
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| TENANT (SUBDOMÍNIO)
|--------------------------------------------------------------------------
*/
Route::domain('{tenant}.faturaja.sdoca')
    ->middleware(['web', ResolveTenant::class])
    ->group(function () {

        // Login do tenant
        Route::post('/authenticate', function (Request $request) {
            if (!Auth::guard('tenant')->attempt($request->only('email', 'password'))) {
                abort(401, 'Credenciais inválidas');
            }
            $request->session()->regenerate();
            return redirect()->route('tenant.dashboard');
        });

        // Rotas protegidas do tenant
        Route::middleware(['auth:tenant', 'tenant.user'])->group(function () {

            // Dashboard
            Route::get('/dashboard', function () {
                return view('tenant.dashboard');
            })->name('tenant.dashboard');

            /*
            |-------------------------
            | CRUD DE USUÁRIOS
            |-------------------------
            */
            Route::prefix('users')->name('tenant.users.')->group(function () {
                Route::get('/', [TenantUserController::class, 'index'])->name('index');
                Route::get('/create', [TenantUserController::class, 'create'])->name('create');
                Route::post('/', [TenantUserController::class, 'store'])->name('store');
                Route::get('/{user}/edit', [TenantUserController::class, 'edit'])->whereUuid('user')->name('edit');
                Route::put('/{user}', [TenantUserController::class, 'update'])->whereUuid('user')->name('update');
                Route::delete('/{user}', [TenantUserController::class, 'destroy'])->whereUuid('user')->name('destroy');
            });

            /*
            |-------------------------
            | CRUD DE CLIENTES
            |-------------------------
            */
            Route::prefix('clients')->name('tenant.clients.')->group(function () {
                Route::get('/', [ClienteWebController::class, 'index'])->name('index');
                Route::get('/create', [ClienteWebController::class, 'create'])->name('create');
                Route::post('/', [ClienteWebController::class, 'store'])->name('store');
                Route::get('/{client}/edit', [ClienteWebController::class, 'edit'])->whereNumber('client')->name('edit');
                Route::put('/{client}', [ClienteWebController::class, 'update'])->whereNumber('client')->name('update');
                Route::delete('/{client}', [ClienteWebController::class, 'destroy'])->whereNumber('client')->name('destroy');
            });

            // Logout do tenant
            Route::post('/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');
        });
});
