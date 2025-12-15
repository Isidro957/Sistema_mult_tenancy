<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TenantUser;

class TenantAuthController extends Controller
{
    /* ==========================
     | FORMULÁRIOS
     ========================== */

    public function showRegisterForm()
    {
        return view('tenant.auth.register');
    }

    public function showLoginForm()
    {
        return view('tenant.auth.login');
    }

    /* ==========================
     | REGISTRO
     ========================== */

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = TenantUser::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        Auth::guard('tenant')->login($user);

        return redirect()->route('tenant.dashboard');
    }

    /* ==========================
     | LOGIN
     ========================== */

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::guard('tenant')->attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas',
            ]);


        }
        
// Previna session fixation
$request->session()->regenerate();

        return redirect()->route('tenant.dashboard');
    }

    /* ==========================
     | LOGOUT
     ========================== */

    public function logout()
    {
        Auth::guard('tenant')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}
