@props([
    'titulo'   => null,
    'texto'    => null,
    'btnTexto' => null,
    'btnUrl'   => null,
])

@php
    $defaultBtnUrl = route('register');
    $defaultTitulo = 'Publicá gratis en TiendaMV';
    $defaultTexto = 'Llega a millones de compradores en todo Chile. Sin comisiones en tu primera venta.';
    $defaultBtnTexto = 'Empezar a vender';

    if (Auth::check()) {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $defaultBtnUrl = route('admin.dashboard');
            $defaultTitulo = 'Administra TiendaMV';
            $defaultTexto = 'Revisa productos, tiendas, usuarios, pedidos y seguridad desde el panel administrador.';
            $defaultBtnTexto = 'Panel admin';
        } elseif ($user->hasRole('vendedor') || $user->tienda) {
            $defaultBtnUrl = route('vendedor.panel');
            $defaultTitulo = 'Gestiona tu tienda';
            $defaultTexto = 'Publica productos, revisa solicitudes y mantén tu tienda al día.';
            $defaultBtnTexto = 'Mi tienda';
        } else {
            $defaultBtnUrl = route('cuenta.perfil');
        }
    }
@endphp

<section class="section home-section home-section-sell">
    <div class="container">
        <div class="cta-banner">
            <div class="cta-content">
                <h2>{{ $titulo ?? $defaultTitulo }}</h2>
                <p>{{ $texto ?? $defaultTexto }}</p>
            </div>
            <a href="{{ $btnUrl ?? $defaultBtnUrl }}" class="cta-btn">{{ $btnTexto ?? $defaultBtnTexto }}</a>
        </div>
    </div>
</section>
