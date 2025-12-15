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
        // Busca todos os usuários do tenant
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
        // Validação usando conexão tenant
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->using(function ($query) {
                    return $query->connection('tenant');
                }),
            ],
            'password' => 'required|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        // Criação do usuário no banco tenant
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
                Rule::unique('users')->ignore($user->id)->using(function ($query) {
                    return $query->connection('tenant');
                }),
            ],
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

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
