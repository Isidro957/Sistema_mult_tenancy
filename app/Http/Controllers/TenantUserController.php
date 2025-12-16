<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\TenantUser;

class TenantUserController extends Controller
{
    /**
     * Listar todos os usuários do tenant
     */
    public function index()
    {
        $users = TenantUser::all();
        return view('tenant.users.index', compact('users'));
    }

    /**
     * Formulário para criar novo usuário
     */
    public function create()
    {
        return view('tenant.users.create');
    }

    /**
     * Salvar novo usuário
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique(TenantUser::class, 'email'),
            ],
            'password' => 'required|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        TenantUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('tenant.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Formulário para editar usuário
     */
    public function edit(TenantUser $user)
    {
        return view('tenant.users.edit', compact('user'));
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, TenantUser $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique(TenantUser::class, 'email')->ignore($user->id),
            ],
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('tenant.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Excluir usuário
     */
    public function destroy(TenantUser $user)
    {
        $user->delete();

        return redirect()->route('tenant.users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
