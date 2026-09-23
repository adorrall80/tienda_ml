<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Acceso' }} — TiendaMV</title>
    @vite(['resources/css/shop.css', 'resources/js/app.js'])
</head>
<body class="auth-body">

<header class="auth-header">
    <a href="{{ route('inicio') }}" class="auth-logo">TiendaMV</a>
</header>

<main class="auth-main">
    <div class="auth-card">
        {{ $slot }}
    </div>
</main>

</body>
</html>
