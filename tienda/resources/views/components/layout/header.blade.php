<header class="header-top" role="banner">
    @php
        $quieroVenderUrl = route('register');
        $quieroVenderLabel = 'Quiero vender';
        $headerUser = Auth::user();
        $hasAdminAccess = false;
        $hasSellerAccess = false;

        if ($headerUser) {
            $hasAdminAccess = $headerUser->hasRole('admin');
            $hasSellerAccess = $headerUser->hasRole(['vendedor']) || (bool) $headerUser->tienda;
            if ($hasAdminAccess) {
                $quieroVenderUrl = route('admin.dashboard');
                $quieroVenderLabel = 'Panel admin';
            } elseif ($hasSellerAccess) {
                $quieroVenderUrl = route('vendedor.panel');
                $quieroVenderLabel = 'Mi tienda';
            } else {
                $quieroVenderUrl = route('cuenta.perfil');
            }
        }
    @endphp

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
                <div class="user-menu" id="userMenu">
                    <button class="user-menu-btn" type="button" id="userMenuBtn" aria-expanded="false">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        <span>{{ Str::limit(Auth::user()->name, 20, '') }}</span>
                        <svg class="chevron" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-user-info">
                            <span class="dropdown-user-email">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="dropdown-divider"></div>
                        @if($hasAdminAccess)
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                                Panel admin
                            </a>
                        @endif
                        @if($hasSellerAccess)
                            <a href="{{ route('vendedor.panel') }}" class="dropdown-item">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                Mi tienda
                            </a>
                        @endif
                        <a href="{{ route('cuenta.perfil') }}" class="dropdown-item">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            Mi cuenta
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-item-danger">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @endguest

            <a href="{{ $quieroVenderUrl }}" class="header-link">{{ $quieroVenderLabel }}</a>
            <a href="{{ route('carrito.index') }}" class="header-link cart-btn" aria-label="Carrito">
                🛒
                <span class="cart-count hidden" aria-live="polite">0</span>
            </a>
        </div>

    </div>
</header>
