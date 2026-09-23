<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Panel' }} — TiendaMV</title>
    @vite(['resources/css/panel.css', 'resources/js/app.js'])
</head>
<body class="panel-body">

{{-- ── TOP NAVBAR ─────────────────────────────────────────── --}}
<nav class="p-topnav">
    <div class="p-topnav-left">
        <button class="p-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <a href="{{ route('inicio') }}" class="p-topnav-brand">
            <span class="p-brand-badge">MV</span>
            <span class="p-brand-name">TiendaMV</span>
        </a>
    </div>

    <div class="p-topnav-right">
        <a href="{{ route('inicio') }}" class="p-topnav-link" title="Ver tienda">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </a>

        <div class="p-user-menu" id="pUserMenu">
            <button class="p-user-btn" id="pUserBtn" type="button">
                <span class="p-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="p-user-name">{{ Str::limit(auth()->user()->name, 18, '') }}</span>
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="p-chevron"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="p-user-dropdown" id="pUserDropdown">
                <div class="p-dropdown-header">
                    <span class="p-dropdown-name">{{ auth()->user()->name }}</span>
                    <span class="p-dropdown-email">{{ auth()->user()->email }}</span>
                </div>
                <div class="p-dropdown-divider"></div>
                <a href="{{ route('cuenta.perfil') }}" class="p-dropdown-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    Mi cuenta
                </a>
                <div class="p-dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-dropdown-item p-dropdown-danger">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="p-wrapper">

    {{-- ── SIDEBAR ──────────────────────────────────────────── --}}
    <aside class="p-sidebar" id="sidebar">
        <nav class="p-sidebar-nav">
            {{ $nav ?? '' }}
        </nav>
    </aside>

    {{-- ── CONTENT ──────────────────────────────────────────── --}}
    <div class="p-content-wrap">

        {{-- Breadcrumb --}}
        <div class="p-breadcrumb-bar">
            <span class="p-breadcrumb-title">{{ $title ?? 'Panel' }}</span>
            <ol class="p-breadcrumb">
                <li><a href="{{ route('inicio') }}">Inicio</a></li>
                <li>{{ $title ?? 'Panel' }}</li>
            </ol>
        </div>

        {{-- Page content --}}
        <main class="p-main">
            @isset($actions)
                <div class="p-page-actions">{{ $actions }}</div>
            @endisset
            {{ $slot }}
        </main>

    </div>
</div>

<script>
(function () {
    // Sidebar toggle
    const toggle = document.getElementById('sidebarToggle');
    const wrapper = document.querySelector('.p-wrapper');
    const mobileQuery = window.matchMedia('(max-width: 640px)');

    const closeSidebarOnMobile = () => {
        if (mobileQuery.matches) {
            wrapper?.classList.add('sidebar-collapsed');
            wrapper?.classList.remove('sidebar-mobile-open');
        }
    };

    closeSidebarOnMobile();
    mobileQuery.addEventListener?.('change', closeSidebarOnMobile);

    toggle?.addEventListener('click', () => {
        if (mobileQuery.matches) {
            wrapper?.classList.toggle('sidebar-mobile-open');
            wrapper?.classList.add('sidebar-collapsed');
            return;
        }

        wrapper?.classList.toggle('sidebar-collapsed');
    });
    document.querySelectorAll('.p-sidebar a').forEach(link => {
        link.addEventListener('click', closeSidebarOnMobile);
    });

    // User dropdown
    const menu = document.getElementById('pUserMenu');
    const btn  = document.getElementById('pUserBtn');
    btn?.addEventListener('click', e => { e.stopPropagation(); menu?.classList.toggle('open'); });
    document.addEventListener('click', e => { if (!menu?.contains(e.target)) menu?.classList.remove('open'); });
})();
</script>

</body>
</html>
