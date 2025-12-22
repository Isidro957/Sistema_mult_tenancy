<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    // Listar clientes do tenant logado
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        $clients = Cliente::where('tenant_id', $tenantId)->get();
        return response()->json($clients);
    }

    // Criar cliente
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefone' => 'nullable|string|max:20',
        ]);

        $client = Cliente::create([
            'tenant_id' => Auth::user()->tenant_id,
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
        ]);

        return response()->json($client, 201);
    }

    // Mostrar cliente
    public function show($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $client = Cliente::where('tenant_id', $tenantId)->findOrFail($id);
        return response()->json($client);
    }

    // Atualizar cliente
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefone' => 'nullable|string|max:20',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $client = Cliente::where('tenant_id', $tenantId)->findOrFail($id);

        $client->update($request->only('nome', 'email', 'telefone'));

        return response()->json($client);
    }

    // Deletar cliente
    public function destroy($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $client = Cliente::where('tenant_id', $tenantId)->findOrFail($id);

        // Se quiser, podemos apenas inativar o cliente
        // $client->update(['status' => false]);
        $client->delete();

        return response()->json(['message' => 'Cliente removido com sucesso']);
    }
}
