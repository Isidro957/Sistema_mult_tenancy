<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host  = $request->getHost();
        $parts = explode('.', $host);

        /**
         * ===============================
         * AMBIENTE LOCAL (localhost)
         * ===============================
         */
        if (in_array($host, ['localhost', '127.0.0.1'])) {

            $tenant = Tenant::first();

            if (! $tenant) {
                abort(500, 'Nenhum tenant cadastrado para ambiente local.');
            }

            $this->setTenantDatabase($tenant);

            return $next($request);
        }

        /**
         * ===============================
         * PRODUÇÃO / SUBDOMÍNIO
         * Ex: empresa1.dominio.com
         * ===============================
         */
        if (count($parts) < 3) {
            abort(404, 'Subdomínio do tenant não encontrado.');
        }

        $subdomain = $parts[0];

        if ($subdomain === 'www') {
            abort(404, 'Subdomínio inválido.');
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (! $tenant) {
            abort(404, "Tenant '{$subdomain}' não existe.");
        }

        $this->setTenantDatabase($tenant);

        return $next($request);
    }

    /**
     * ===============================
     * CONFIGURA O BANCO DO TENANT
     * ===============================
     */
    private function setTenantDatabase(Tenant $tenant): void
    {
        config([
            'database.connections.tenant.database' => $tenant->database_name,
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // Disponível globalmente
        app()->instance('tenant', $tenant);
    }
}
