<header class="header-top" role="banner">
    <div class="container">

        <button class="mobile-menu-btn" aria-label="Abrir menú">☰</button>

        <a href="{{ route('inicio') }}" class="logo" aria-label="{{ config('app.name') }} inicio">
            {{ config('app.name') }}
        </a>

        <form class="search-form" role="search" action="{{ route('productos.index') }}" method="get">
            <select class="search-category" name="cat" aria-label="Categoría">
                <option value="">Todas las categorías</option>
                @foreach(App\Models\Category::activas()->raiz()->get() as $cat)
                    <option value="{{ $cat->slug }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
            <div class="search-input-wrap">
                <input class="search-input" type="search" name="q"
                       value="{{ request('q') }}"
                       placeholder="Buscar productos, marcas y más…"
                       autocomplete="off"
                       aria-label="Buscar">
                <div class="search-autocomplete" role="listbox"></div>
            </div>
            <button class="search-btn" type="submit" aria-label="Buscar">🔍</button>
        </form>

        <div class="header-actions">
            <button class="location-btn" aria-label="Ubicación">
                <span>📍</span>
                <span>Santiago</span>
            </button>

            @guest
                <a href="{{ route('login') }}" class="header-link">Ingresa</a>
                <a href="{{ route('register') }}" class="header-link highlight">Crea tu cuenta</a>
            @else
                <a href="{{ route('dashboard') }}" class="header-link">{{ Auth::user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="header-link">Salir</button>
                </form>
            @endguest

            <a href="#" class="header-link">Vende</a>
            <a href="{{ route('carrito.index') }}" class="header-link cart-btn" aria-label="Carrito">
                🛒
                <span class="cart-count hidden" aria-live="polite">0</span>
            </a>
        </div>

    </div>
</header>
