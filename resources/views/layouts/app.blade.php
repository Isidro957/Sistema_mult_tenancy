<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FaturaJa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            min-width: 220px;
            max-width: 220px;
            background-color: #C9B6E4;
            color: #fff;
            min-height: 100vh;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #B497D9;
            text-decoration: none;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .card {
            border-radius: 12px;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center">FaturaJa</h4>
        <hr>
        <p><strong>Tenant:</strong> {{ app('tenant')->name }}</p>
        <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="{{ route('tenant.dashboard') }}" class="nav-link">Dashboard</a></li>
            <li class="nav-item mb-2"><a href="{{ route('tenant.users.index') }}" class="nav-link">Usuários</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link">Configurações</a></li>
            <li class="nav-item mt-4"><a href="{{ route('tenant.logout') }}" class="nav-link text-danger" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
        </ul>
        <form id="logout-form" action="{{ route('tenant.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Main content -->
    <div class="content w-100">
        @yield('content')
    </div>
</div>

<scrip
