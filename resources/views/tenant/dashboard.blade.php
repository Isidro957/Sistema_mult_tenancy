<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tenant</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
        }
        .btn-primary {
            background-color: #6f42c1;
            border: none;
        }
        .btn-primary:hover {
            background-color: #5a34a1;
        }
        .btn-outline-primary {
            border-color: #6f42c1;
            color: #6f42c1;
        }
        .btn-outline-primary:hover {
            background-color: #6f42c1;
            color: #fff;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('tenant.dashboard') }}">
            Dashboard Tenant
        </a>

        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light">
                Sair
            </button>
        </form>
    </div>
</nav>

<div class="container">

    <!-- DADOS DO USUÁRIO -->
    <div class="card shadow-sm p-4 mb-4">
        <h4 class="mb-3">👤 Dados do Usuário</h4>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Nome:</strong> {{ auth('tenant')->user()->name }}</p>
                <p><strong>Email:</strong> {{ auth('tenant')->user()->email }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Função:</strong> {{ auth('tenant')->user()->role }}</p>
                <p><strong>ID:</strong> {{ auth('tenant')->user()->id }}</p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Criado em:</strong> {{ auth('tenant')->user()->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Atualizado em:</strong> {{ auth('tenant')->user()->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- AÇÕES PRINCIPAIS -->
    <div class="card shadow-sm p-4 mb-4">
        <h4 class="mb-3">⚙️ Gerenciamento</h4>

        <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('tenant.users.index') }}" class="btn btn-primary">
                Gerenciar Usuários
            </a>

            <a href="{{ route('tenant.clients.index') }}" class="btn btn-outline-primary">
                Gerenciar Clientes
            </a>
        </div>
    </div>

    <!-- INFORMAÇÕES DO TENANT -->
    <div class="card shadow-sm p-4">
        <h4 class="mb-3">🏢 Informações da Empresa</h4>

        <table class="table table-bordered mb-0">
            <tr>
                <th>Nome</th>
                <td>{{ app('tenant')->name }}</td>
            </tr>
            <tr>
                <th>Subdomínio</th>
                <td>{{ app('tenant')->subdomain }}</td>
            </tr>
            <tr>
                <th>Base de Dados</th>
                <td>{{ app('tenant')->database_name }}</td>
            </tr>
            <tr>
                <th>Criado em</th>
                <td>{{ app('tenant')->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
