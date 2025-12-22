<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\TenantUser;
use Illuminate\Auth\Events\Registered;

class TenantUserController extends Controller
{
    public function index()
    {
        $tenantId = app('tenant')->id;
        $users = TenantUser::where('tenant_id', $tenantId)->get();
        return view('tenant.users.index', compact('users'));
    }

    public function create()
    {
        return view('tenant.users.create');
    }

    public function store(Request $request)
    {
        $tenantId = app('tenant')->id;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique(TenantUser::class, 'email')->where(fn($q) => $q->where('tenant_id', $tenantId)),
            ],
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,cliente',
        ]);

        $user = TenantUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tenant_id' => $tenantId,
        ]);

        $user->sendEmailVerificationNotification();
        event(new Registered($user));

        return redirect()->route('tenant.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit($user)
    {
        $tenantId = app('tenant')->id;
        $user = TenantUser::where('tenant_id', $tenantId)->where('id', $user)->firstOrFail();
        return view('tenant.users.edit', compact('user'));
    }

    public function update(Request $request, $user)
    {
        $tenantId = app('tenant')->id;
        $user = TenantUser::where('tenant_id', $tenantId)->where('id', $user)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique(TenantUser::class, 'email')->ignore($user->id)->where(fn($q) => $q->where('tenant_id', $tenantId)),
            ],
            'role' => 'required|in:admin,cliente',
        ]);

        $user->update($request->only('name','email','role'));

        return redirect()->route('tenant.users.index')->with('success', 'Usuário atualizado!');
    }

    public function destroy($user)
    {
        $tenantId = app('tenant')->id;
        $user = TenantUser::where('tenant_id', $tenantId)->where('id', $user)->firstOrFail();

        if(auth()->guard('tenant')->id() === $user->id){
            return redirect()->back()->with('error', 'Você não pode deletar sua própria conta!');
        }

        $user->delete();
        return redirect()->route('tenant.users.index')->with('success', 'Usuário deletado!');
    }
}
