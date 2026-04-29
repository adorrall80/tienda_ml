<div class="mobile-nav-overlay"></div>
<nav class="mobile-nav" aria-label="Menú móvil">
    <div class="mobile-nav-header">
        <span class="logo">{{ config('app.name') }}</span>
        <button class="mobile-nav-close" aria-label="Cerrar menú">✕</button>
    </div>
    <div class="mobile-nav-user">
        @guest
            <a href="{{ route('login') }}">Ingresa a tu cuenta</a>
            <a href="{{ route('register') }}">Crea tu cuenta gratis</a>
        @else
            <a href="{{ route('dashboard') }}">{{ Auth::user()->name }}</a>
        @endguest
    </div>
    <div class="mobile-nav-list">
        @foreach(App\Models\Category::activas()->raiz()->get() as $cat)
            <a href="{{ route('productos.index', ['cat' => $cat->slug]) }}">
                {{ $cat->icono }} {{ $cat->nombre }}
            </a>
        @endforeach
        <a href="{{ route('carrito.index') }}">🛒 Mi carrito</a>
    </div>
</nav>
