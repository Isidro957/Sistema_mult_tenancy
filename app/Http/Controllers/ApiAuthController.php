<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\TenantUser;

class ApiAuthController extends Controller
{
    /**
     * LOGIN GLOBAL
     * Descobre o tenant pelo email
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        /**
         * Descobrir o tenant correto
         */
        $tenant = Tenant::all()->first(function ($tenant) use ($request) {

            config([
                'database.connections.tenant.database' => $tenant->database_name,
            ]);

            DB::purge('tenant');
            DB::reconnect('tenant');

            return DB::connection('tenant')
                ->table('users')
                ->where('email', $request->email)
                ->exists();
        });

        if (! $tenant) {
            return response()->json([
                'message' => 'Usuário não pertence a nenhuma empresa'
            ], 404);
        }

        /**
         * Garantir conexão correta do tenant
         */
        config([
            'database.connections.tenant.database' => $tenant->database_name,
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        /**
         * Buscar usuário
         */
        $user = TenantUser::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        /**
         * Criar token Sanctum
         */
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token'        => $token,
            'tenant'       => $tenant->subdomain,
            'user'         => $user,
            'redirect_api' => "https://{$tenant->subdomain}.faturaja.sdoca/api"
        ]);
    }

    /**
     * REGISTRO (já dentro do tenant)
     */
    public function register(Request $request)
    {
        $tenant = app('tenant');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = TenantUser::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'tenant_id' => $tenant->id,
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user'    => $user,
        ], 201);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }
}
