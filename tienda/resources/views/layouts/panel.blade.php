<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Panel' }} — TiendaMV</title>
    @vite(['resources/css/panel.css', 'resources/js/app.js'])
</head>
<body class="panel-body">

<aside class="panel-sidebar">
    <div class="panel-brand">
        <a href="{{ route('inicio') }}">
            <span class="brand-logo">MV</span>
            <span class="brand-text">TiendaMV</span>
        </a>
    </div>

    <nav class="panel-nav">
        {{ $nav ?? '' }}
    </nav>

    <div class="panel-sidebar-footer">
        <span class="sidebar-user">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Salir</button>
        </form>
    </div>
</aside>

<div class="panel-main">
    <header class="panel-topbar">
        <h1 class="panel-title">{{ $title ?? 'Panel' }}</h1>
        @isset($actions)
            <div class="panel-actions">{{ $actions }}</div>
        @endisset
    </header>

    <div class="panel-content">
        {{ $slot }}
    </div>
</div>

</body>
</html>
